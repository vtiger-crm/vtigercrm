/********************************************************************
 *                                                                  *
 *  Title:  pfm2afm - Convert Windows .pfm files to .afm files      *
 *                                                                  *
 *  Author: Ken Borgendale   10/9/91  Version 1.0                   *
 *                                                                  *
 *  Function:                                                       *
 *      Convert a Windows .pfm (Printer Font Metrics) file to a     *
 *      .afm (Adobe Font Metrics) file.  The purpose of this is     *
 *      to allow fonts put out for Windows to be used with OS/2.    *
 *                                                                  *
 *  Syntax:                                                         *
 *      pfm2afm  infile  [outfile] -a                               *
 *                                                                  *
 *  Copyright:                                                      *
 *      pfm2afm - Copyright (C) IBM Corp., 1991                     *
 *                                                                  *
 *      This code is released for public use as long as the         *
 *      copyright remains intact.  This code is provided asis       *
 *      without any warrenties, express or implied.                 *
 *                                                                  *
 *  Notes:                                                          *
 *      1. Much of the information in the original .afm file is     *
 *         lost when the .pfm file is created, and thus cannot be   *
 *         reconstructed by this utility.  This is especially true  *
 *         of data for characters not in the Windows character set. *
 *                                                                  *
 *      2. This module is coded to be compiled by the MSC 6.0.      *
 *         For other compilers, be careful of the packing of the    *
 *         PFM structure.                                           *
 *                                                                  *
 ********************************************************************/


/********************************************************************
 *  Modified:  Russell Lang <rjl@eng.monash.edu.au>                 *
 *             1994-01-06  Version 1.1                              *
 *  Compiles with EMX/GCC                                           *
 *  Changed to AFM 3.0                                              *
 *  Put PFM Copyright in Notice instead of Comment                  *
 *  Added ItalicAngle                                               *
 *  Added UnderlinePosition                                         *
 *  Added UnderlineThickness                                        *
 *                                                                  *
 *  Modified 1995-03-10  rjl (fixes from Norman Walsh)              *
 *  Dodge compiler bug when creating descender                      *
 ********************************************************************/


/********************************************************************
 *  Modified:  Olivier Plathey <olivier@fpdf.org>                   *
 *             2002-08-10  Version 1.11                             *
 *  Compiles with MinGW                                             *
 *  Output Descender as negative value                              *
 *  Removed double spaces in character entries                      *
 *                                                                  *
 *             2005-03-12  Version 1.12                             *
 *  Doubled size of BUFSIZE                                         *
 ********************************************************************/

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include "pfm2afm.h"

#define BUFSIZE 8192
/*
 *  Function Prototypes
 */
void  help (void);
void  parseargs (int argc, uchar * * argv);
void  openpfm(void);
void  openafm(void);
void  putheader(void);
void  putchartab(void);
void  outchar(int code, ushort width, const uchar * name);
void  putkerntab(KERN * kerntab, int kerncnt);
void  puttrailer(void);
void  outval(int val);
void  outreal(float val);

/*
 *  Global variables
 */
FILE   * inf;                /* Input file */
FILE   * outf;               /* Output file */
uchar  infname[272];         /* Input file name */
uchar  outfname[272];        /* Output file name */
uchar  * buffer;             /* .pfm read buffer */
PFM    * pfm;                /* .pfm header */
PSX    * psx;                /* Metrics extension */

uchar    debugflag;          /* Debug information flag */
uchar    allflag;
uchar    isMono;             /* Font is mono-spaced */

/*
 *  Do the function
 */
MAINENT main(int argc, uchar * *argv) {

    /* Parse arguments */
    parseargs(argc, argv);

    /* Open and check input file */
    openpfm();

    /* Make output file name and open */
    openafm();

    /* Put out header information */
    putheader();

    /* Put out character table */
    putchartab();

    /* Put out kerning table */
    if (pfm->kernpairs) {
        putkerntab((KERN *)(buffer+pfm->kernpairs+2),
                   *(ushort *)(buffer+pfm->kernpairs));
    }
    if (pfm->kerntrack) { /* rjl */
	fprintf(stderr, "Ignoring track kern table\n");
    }

    /* Put out trailer line */
    puttrailer();

    /* Cleanup */
    if (buffer)
        free(buffer);
    fclose(inf);
    fclose(outf);
    return 0;
}

/*
 *  Put out normal help
 */
void  help (void) {
    puts("\npfm2afm - Convert Windows pfm to afm - Version 1.11\n");
    puts("This utility converts Windows pfm files for Adobe type 1 fonts");
    puts("to afm files for use on OS/2.  This allows fonts created for");
    puts("Windows, and shipped without the afm file to be used on OS/2.\n");
    puts("pfm2afm  infile  [outfile]  -opts");
    puts("    The extension .pfm is added to the infile if it has none.");
    puts("    The outfile is defaulted from the input file name.");
    puts("    -a = All codepoints in range");
    puts("\nNote that pfm files are missing some of the data necessary to");
    puts("construct afm files, so the conversion may not be perfect.\n");
    puts("Ken Borgendale  -  kwb@betasvm2.vnet.ibm.com\n");
    puts("Russell Lang    -  rjl@monu1.cc.monash.edu.au\n");
    exit (1);
}


/*
 *  Parse arguments.  This is the full arg treatment, which is sort of
 *  overkill for one option, but it allows more to be added later.
 */
void  parseargs (int argc, uchar * * argv) {
    uchar  swchar;
    int    argcnt;
    int    filecnt;
    uchar * argp;

    argcnt = 1;
    filecnt = 0;
    /* Read the arguments and decide what we are doing */
    while (argcnt<argc) {
        argp = argv[argcnt];
        /* Check for switches.  Files may not start with - or / */
        if (*argp == '-' || *argp == OPTSEP) {
            /* Process switches */
            swchar = (uchar)tolower(argp[1]);
            argp += 2;
            switch (swchar) {
            case '?':
                help();      /* Does not return */

            /* All codepoints */
            case 'a':
                allflag = 0;
                break;

            /* Debug option */
            case 'd':
                debugflag = 1;
                break;

            default:
                fputs("Unknown options: ", stderr);
                fputs(argp-2, stderr);
                fputc('\n', stderr);
            }
        } else {
            if (*argp=='?') {
                help();      /* Does not return */
            }
            switch(++filecnt) {
            case 1:
                strcpy(infname, argp);
                break;
            case 2:
                strcpy(outfname, argp);
                break;
            default:
                fputs("Extra parameter ignored: ", stderr);
                fputs(argp, stderr);
                fputc('\n', stderr);
            }
        }
        argcnt++;
    }

    /* We require the input file name */
    if (!filecnt) help();
}


/*
 *  Open the .pfm file and check it
 */
void  openpfm(void) {
    uchar   * cp;
    int       len;

    /* Check for a file extension */
    cp = infname+strlen(infname)-1;
    while (cp>=infname && *cp!='.' && *cp!='\\' && *cp!='/' && *cp!=':')
       cp--;
    if (*cp!='.')
        strcat(infname, ".pfm");
    /* Open the file */
    inf = fopen(infname, "rb");
    if (!inf) {
        fputs("Unable to open input file - ", stderr);
        fputs(infname, stderr);
        fputc('\n', stderr);
        exit(4);
    }
    /* Read the file */
    buffer = malloc(BUFSIZE);
    len = fread(buffer, 1, BUFSIZE, inf);
    if (len<256 || len==BUFSIZE) {
        fputs("Input file read error - ", stderr);
        fputs(infname, stderr);
        fputc('\n', stderr);
        exit(6);
    }
    /* Do consistency check */
    pfm = (PFM *) buffer;
    if (len != (int)pfm->len &&  /* Check length field matches file length */
        pfm->extlen != 30 &&     /* Check length of PostScript extension   */
        pfm->fontname>75 && pfm->fontname<512) {  /* Font name specified */
        fputs("Not a valid Windows type 1 .pfm file - ", stderr);
        fputs(infname, stderr);
        fputc('\n', stderr);
        exit(6);
    }
}

/*
 *  Create the .afm file
 */
void  openafm(void) {
    uchar  * cp;

    /* Add .pfm if there is none */
    if (!*outfname) {
        strcpy(outfname, infname);
        cp = outfname+strlen(outfname)-1;
        while (cp >= outfname && *cp!='.' && *cp!='\\' && *cp!='/' && *cp!=':')
           cp--;
        if (*cp=='.') *cp=0;
        strcat(outfname, ".afm");
    }
    /* Open the file */
    outf = fopen(outfname, "w");
    if (!outf) {
        fputs("Unable to open output file - ", stderr);
        fputs(outfname, stderr);
        fputc('\n', stderr);
        exit(5);
    }
}

/*
 *  Put out the header of the .afm file
 */
void  putheader(void) {
    uchar * cp;
    int temp;  /* rjl 1995-03-10 */

    fputs("StartFontMetrics 3.0\n", outf);
    if (*pfm->copyright) {
        fputs("Notice ", outf);
        fputs(pfm->copyright, outf);
        fputc('\n', outf);
    }
    fputs("FontName ", outf);
    fputs(buffer+pfm->fontname, outf);
    fputs("\nEncodingScheme ", outf);
    if (pfm->charset) {
        fputs("FontSpecific\n", outf);
    } else {
        fputs("AdobeStandardEncoding\n", outf);
    }
    /*
     * The .pfm is missing full name, so construct from font name by
     * changing the hyphen to a space.  This actually works in a lot
     * of cases.
     */
    fputs("FullName ", outf);
    cp = buffer+pfm->fontname;
    while (*cp) {
        if (*cp=='-') *cp=' ';
        fputc(*cp, outf);
        cp++;
    }
    if (pfm->face) {
        fputs("\nFamilyName ", outf);
        fputs(buffer+pfm->face, outf);
    }

    fputs("\nWeight ", outf);
    if (pfm->weight>475) fputs("Bold", outf);
    else if (pfm->weight<325 && pfm->weight)
        fputs("Light", outf);
    else fputs("Medium", outf);

    /*
     *  The mono flag in the pfm actually indicates whether there is a
     *  table of font widths, not if they are all the same.
     */
    fputs("\nIsFixedPitch ", outf);
    if (!(pfm->kind&1) ||                  /* Flag for mono */
        pfm->avgwidth == pfm->maxwidth ) {  /* Avg width = max width */
        fputs("true", outf);
        isMono = 1;
    } else {
        fputs("false", outf);
        isMono = 0;
    }

    /*
     * The font bounding box is lost, but try to reconstruct it.
     * Much of this is just guess work.  The bounding box is required in
     * the .afm, but is not used by the PM font installer.
     */
    psx = (PSX *)(buffer+pfm->psext);
    fputs("\nFontBBox", outf);
    if (isMono) outval(-20);      /* Just guess at left bounds */
    else outval(-100);
    temp = psx->descender; /* rjl 1995-03-10 */
    temp = -(temp+5); /* rjl 1995-03-10 */
    outval(temp);  /* Descender is given as positive value */ /* rjl 1995-03-10 */
    /*     outval(-(psx->descender+5));  /* Descender is given as positive value */
    outval(pfm->maxwidth+10);
    outval(pfm->ascent+5);

    /*
     * Give other metrics that were kept
     */
    fputs("\nCapHeight", outf);
    outval((int)psx->capheight);
    fputs("\nXHeight", outf);
    outval((int)psx->xheight);
    fputs("\nDescender", outf);
    outval((int)-psx->descender); /* output negative value*/
    fputs("\nAscender", outf);
    /* outval((int)psx->ascender); */
    outval((int)pfm->ascent); /* rjl */
    /* extra keys added by rjl */
    if (psx->len >= sizeof(psx)) {
	fputs("\nItalicAngle", outf);
        outreal(psx->slant/10.0);
	fputs("\nUnderlinePosition", outf);
        outval((int)-psx->underlineoffset);
	fputs("\nUnderlineThickness", outf);
        outval((int)psx->underlinewidth);
    }
    fputc('\n', outf);
}

/*
 *  Put out the character tables.  According to the .afm spec, the
 *  characters must be put out sorted in encoding order.
 *
 *  Most Windows .pfm files have the characters in the range 20-ff in
 *  the Windows code page (819 + quotes).
 */
void  putchartab(void) {
    int    count, i, j;
    ushort spwidth;
    ushort * ctab;
    uchar  back[256];

    /*
     * Compute the count by getting rid of non-existant chars.  This
     * is complicated by the fact that Windows encodes the .pfm file
     * with a space metric for non-existant chars.
     */
    memset(back, 0, 256);
    count = pfm->lastchar - pfm->firstchar + 1;
    spwidth = 0;
    /* Compute width of space */
    ctab = (ushort *)(buffer+pfm->chartab);
    if (pfm->firstchar>=' ' && pfm->lastchar<=' ') {
        spwidth = ctab[' '-pfm->firstchar];
    }

    if (!pfm->charset) {
        /*
         *  Loop thru the chars, deleting those that we presume
         *  do not really exist.
         */
        for (i=pfm->firstchar; i<=(int)pfm->lastchar; i++) {
            if (Win2PSStd[i]) {
                back[Win2PSStd[i]] = (uchar)i;
            } else {
                if (!allflag) {
                    if (*ctab==spwidth) {   /* Default width */
                        if (!(WinClass[i]&1)) {
                            *ctab = 0;
                            count--;
                        }
                    } else {                /* Not default width */
                        if (!WinClass[i]) {
                            *ctab = 0;
                            count--;
                        }
                    }
                }
            }
            ctab++;
        }
    }

    /* Put out the header */
    fputs("StartCharMetrics", outf);
    outval(count);
    fputc('\n', outf);

    /* Put out all encoded chars */
    if (pfm->charset) {
    /*
     * If the charset is not the Windows standard, just put out
     * unnamed entries.
     */
        ctab = (ushort *)(buffer+pfm->chartab);
        for (i=pfm->firstchar; i<=(int)pfm->lastchar; i++) {
            if (*ctab) {
                outchar(i, *ctab, NULL);
            }
            ctab++;
        }
    } else {
        ctab = (ushort *)(buffer+pfm->chartab);
        for (i=0; i<256; i++) {
            j = back[i];
            if (j) {
                outchar(i, ctab[j-pfm->firstchar], WinChars[j]);
                ctab[j-pfm->firstchar] = 0;
            }
        }
        /* Put out all non-encoded chars */
        ctab = (ushort *)(buffer+pfm->chartab);
        for (i=pfm->firstchar; i<=(int)pfm->lastchar; i++) {
            if (*ctab) {
                outchar(-1, *ctab, WinChars[i]);
            }
            ctab++;
        }
    }
    /* Put out the trailer */
    fputs("EndCharMetrics\n", outf);
}

/*
 *  Output a character entry
 */
void  outchar(int code, ushort width, const uchar * name) {
    fputs("C", outf);
    outval(code);
    fputs(" ; WX", outf);
    outval(width);
    if (name) {
        fputs(" ; N ", outf);
        fputs(name, outf);
    }
    fputs(" ;\n", outf);
}

/*
 *  Put out the kerning tables
 */
void  putkerntab(KERN * kerntab, int kerncnt) {
    int    count, i;
    KERN * kp;

    /* Count non-zero kern pairs */
    count = kerncnt;
    kp = kerntab;
    for (i=0; i<kerncnt; i++) {
        if (!kp->kern)
            count--;
        kp++;
    }

    /* Put out header */
    fputs("StartKernData\nStartKernPairs", outf);
    outval(count);
    fputc('\n', outf);

    /* Put out each non-zero pair */
    kp = kerntab;
    while (kerncnt) {
        if (kp->kern) {
            fputs("KPX ", outf);
            fputs(WinChars[kp->first], outf);
            fputc(' ', outf);
            fputs(WinChars[kp->second], outf);
            outval((int)kp->kern);
            fputc('\n', outf);
        }
        kp++;
        kerncnt--;
    }

    /* Put out trailer */
    fputs("EndKernPairs\nEndKernData\n", outf);
}

/*
 *  Put out the trailer of the .afm file
 */
void  puttrailer(void) {
    fputs("EndFontMetrics\n", outf);
}

/*
 *  Output a decimal value
 */
void outval(int v) {
    char chx[16];
#ifdef unix
    sprintf(chx, "%d", v);  /* rjl 1995-03-10 */
#else
    itoa(v, chx, 10);
#endif
    fputc(' ', outf);
    fputs(chx, outf);
}

/*
 *  Output a real value
 */
void outreal(float v) {
    fprintf(outf," %g",v);
}
