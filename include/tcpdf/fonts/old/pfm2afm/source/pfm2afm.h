/********************************************************************
 *                                                                  *
 *  Title:  pfm2afm - Convert Windows .pfm files to .afm files      *
 *                                                                  *
 *  Author: Ken Borgendale   10/9/91                                *
 *                                                                  *
 *  Function:                                                       *
 *      Declare types and constants for pfm2afm.                    *
 *                                                                  *
 *  Copyright:                                                      *
 *      pfm2afm - Copyright (C) IBM Corp., 1991                     *
 *                                                                  *
 *      This code is released for public use as long as the         *
 *      copyright remains intact.  This code is provided asis       *
 *      without any warrenties, express or implied.                 *
 *                                                                  *
 *  Note:                                                           *
 *      1. Be very careful of the packing of the PFM structure.     *
 *         This is a very badly designed data structure.  (I just   *
 *         read it, Microsoft designed it).                         *
 *                                                                  *
 ********************************************************************/

/********************************************************************
 *  Modified:  Russell Lang <rjl@eng.monash.edu.au>                 *
 *             1994-01-06  Version 1.1                              *
 *  Compiles with EMX/GCC                                           *
 *  Added ItalicAngle                                               *
 *  Added UnderlinePosition                                         *
 *  Added UnderlineThickness                                        *
 *  Modified: rjl 1995-03-10 (fixes from Norman Walsh)              *
 *  Added unix conditional                                          *
 ********************************************************************/


/********************************************************************
 *  Modified:  Olivier Plathey <olivier@fpdf.org>                   *
 *             2002-08-10  Version 1.11                             *
 *  Added WIN32 conditional                                         *
 ********************************************************************/
/*
 *  Define unsigned types and other compiler sensitive stuff
 */
typedef unsigned char   uchar;
typedef unsigned short  ushort;
typedef unsigned long   ulong;
typedef unsigned int    uint;
#if defined(__EMX__) || defined(unix) || defined(WIN32)
#if defined(__EMX__)
#define itoa(a,b,c) _itoa(a,b,c)
#endif
#define NEAR
#define MAINENT int
#else
#define NEAR near
#define MAINENT _cdecl
#endif
#define OPTSEP  '/'
#define PATHSEP ';'

/*
 *  Declare Windows .pfm structure.  Many fields are not declared if
 *  they are not used by this program.
 */

/*
 * This structure was created with no thought to alignment, so we must
 * set the alignment to character.
 */
#pragma pack(1)
typedef struct pfm_ {
    ushort  vers;
    ulong   len;             /* Total length of .pfm file */
    uchar   copyright[60];   /* Copyright string */
    ushort  type;
    ushort  points;
    ushort  verres;
    ushort  horres;
    ushort  ascent;
    ushort  intleading;
    ushort  extleading;
    uchar   italic;
    uchar   uline;
    uchar   overs;
    ushort  weight;
    uchar   charset;         /* 0=windows, otherwise nomap */
    ushort  pixwidth;        /* Width for mono fonts */
    ushort  pixheight;
    uchar   kind;            /* Lower bit off in mono */
    ushort  avgwidth;        /* Mono if avg=max width */
    ushort  maxwidth;        /* Use to compute bounding box */
    uchar   firstchar;       /* First char in table */
    uchar   lastchar;        /* Last char in table */
    uchar   defchar;
    uchar   brkchar;
    ushort  widthby;
    ulong   device;
    ulong   face;            /* Face name */
    ulong   bits;
    ulong   bitoff;
    ushort  extlen;
    ulong   psext;           /* PostScript extension */
    ulong   chartab;         /* Character width tables */
    ulong   res1;
    ulong   kernpairs;       /* Kerning pairs */
    ulong   kerntrack;	     /* Track Kern table */ /* rjl */
    ulong   fontname;        /* Font name */
} PFM;
#pragma pack()

/*
 *  Some metrics from the PostScript extension
 */
typedef struct psx_ {
    ushort  len;
    uchar   res1[12];
/*  uchar   res1[14];           replaced by above two lines by rjl */
    ushort  capheight;       /* Cap height */
    ushort  xheight;         /* X height */
    ushort  ascender;        /* Ascender */
    ushort  descender;       /* Descender (positive) */
    /* extra entries added by rjl */
    short  slant;	     /* CW italic angle */
    short  superscript;
    short  subscript;
    short  superscriptsize;
    short  subscriptsize;
    short  underlineoffset;  /* +ve down */
    short  underlinewidth;   /* width of underline */
} PSX;

/*
 *  Kerning pairs
 */
typedef struct kern_ {
    uchar   first;           /* First character */
    uchar   second;          /* Second character */
    short   kern;            /* Kern distance */
} KERN;

/*
 * Translate table from 1004 to psstd.  1004 is an extension of the
 * Windows translate table used in PM.
 */
uchar Win2PSStd[] = {
  0,   0,   0,   0, 197, 198, 199,   0, 202,   0, 205, 206, 207,   0,   0,   0,
  0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0,
 32,  33,  34,  35,  36,  37,  38, 169,  40,  41,  42,  43,  44,  45,  46,  47,
 48,  49,  50,  51,  52,  53,  54,  55,  56,  57,  58,  59,  60,  61,  62,  63,
 64,  65,  66,  67,  68,  69,  70,  71,  72,  73,  74,  75,  76,  77,  78,  79,
 80,  81,  82,  83,  84,  85,  86,  87,  88,  89,  90,  91,  92,  93,  94,  95,
193,  97,  98,  99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111,
112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127,
  0,   0, 184,   0, 185, 188, 178, 179,  94, 189,   0, 172, 234,   0,   0,   0,
  0,  96,   0, 170, 186,   0, 177, 208, 126,   0,   0, 173, 250,   0,   0,   0,
  0, 161, 162, 163, 168, 165,   0, 167, 200,   0, 227, 171,   0,   0,   0,   0,
  0,   0,   0,   0, 194,   0, 182, 180, 203,   0, 235, 187,   0,   0,   0, 191,
  0,   0,   0,   0,   0,   0, 225,   0,   0,   0,   0,   0,   0,   0,   0,   0,
  0,   0,   0,   0,   0,   0,   0,   0, 233,   0,   0,   0,   0,   0,   0, 251,
  0,   0,   0,   0,   0,   0, 241,   0,   0,   0,   0,   0,   0,   0,   0,   0,
  0,   0,   0,   0,   0,   0,   0,   0, 249,   0,   0,   0,   0,   0,   0,   0,
};

/*
 *  Character class.  This is a minor attempt to overcome the problem that
 *  in the pfm file, all unused characters are given the width of space.
 */
uchar WinClass[] = {
    0, 0, 0, 0, 2, 2, 2, 0, 2, 0, 2, 2, 2, 0, 0, 0,   /* 00 */
    0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,   /* 10 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* 20 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* 30 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* 40 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* 50 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* 60 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2,   /* 70 */
    0, 0, 2, 0, 2, 2, 2, 2, 2, 2, 2, 2, 2, 0, 0, 0,   /* 80 */
    0, 3, 3, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 0, 0, 2,   /* 90 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* a0 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* b0 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* c0 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* d0 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* e0 */
    1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,   /* f0 */
};

/*
 *  Windows chararacter names.  Give a name to the usused locations
 *  for when the all flag is specified.
 */
uchar NEAR * WinChars[] = {
    "W00",              /*   00    */
    "W01",              /*   01    */
    "W02",              /*   02    */
    "W03",              /*   03    */
    "macron",           /*   04    */ /* ?? */
    "breve",            /*   05    */ /* ?? */
    "dotaccent",        /*   06    */ /* ?? */
    "W07",              /*   07    */
    "ring",             /*   08    */ /* ?? */
    "W09",              /*   09    */
    "W0a",              /*   0a    */
    "W0b",              /*   0b    */
    "W0c",              /*   0c    */
    "W0d",              /*   0d    */
    "W0e",              /*   0e    */
    "W0f",              /*   0f    */
    "hungarumlaut",     /*   10    */ /* ?? */
    "ogonek",           /*   11    */ /* ?? */
    "caron",            /*   12    */ /* ?? */
    "W13",              /*   13    */
    "W14",              /*   14    */
    "W15",              /*   15    */
    "W16",              /*   16    */
    "W17",              /*   17    */
    "W18",              /*   18    */
    "W19",              /*   19    */
    "W1a",              /*   1a    */
    "W1b",              /*   1b    */
    "W1c",              /*   1c    */
    "W1d",              /*   1d    */
    "W1e",              /*   1e    */
    "W1f",              /*   1f    */
    "space",            /*   20    */
    "exclam",           /*   21    */
    "quotedbl",         /*   22    */
    "numbersign",       /*   23    */
    "dollar",           /*   24    */
    "percent",          /*   25    */
    "ampersand",        /*   26    */
    "quotesingle",      /*   27    */
    "parenleft",        /*   28    */
    "parenright",       /*   29    */
    "asterisk",         /*   2A    */
    "plus",             /*   2B    */
    "comma",            /*   2C    */
    "hyphen",           /*   2D    */
    "period",           /*   2E    */
    "slash",            /*   2F    */
    "zero",             /*   30    */
    "one",              /*   31    */
    "two",              /*   32    */
    "three",            /*   33    */
    "four",             /*   34    */
    "five",             /*   35    */
    "six",              /*   36    */
    "seven",            /*   37    */
    "eight",            /*   38    */
    "nine",             /*   39    */
    "colon",            /*   3A    */
    "semicolon",        /*   3B    */
    "less",             /*   3C    */
    "equal",            /*   3D    */
    "greater",          /*   3E    */
    "question",         /*   3F    */
    "at",               /*   40    */
    "A",                /*   41    */
    "B",                /*   42    */
    "C",                /*   43    */
    "D",                /*   44    */
    "E",                /*   45    */
    "F",                /*   46    */
    "G",                /*   47    */
    "H",                /*   48    */
    "I",                /*   49    */
    "J",                /*   4A    */
    "K",                /*   4B    */
    "L",                /*   4C    */
    "M",                /*   4D    */
    "N",                /*   4E    */
    "O",                /*   4F    */
    "P",                /*   50    */
    "Q",                /*   51    */
    "R",                /*   52    */
    "S",                /*   53    */
    "T",                /*   54    */
    "U",                /*   55    */
    "V",                /*   56    */
    "W",                /*   57    */
    "X",                /*   58    */
    "Y",                /*   59    */
    "Z",                /*   5A    */
    "bracketleft",      /*   5B    */
    "backslash",        /*   5C    */
    "bracketright",     /*   5D    */
    "asciicircum",      /*   5E    */
    "underscore",       /*   5F    */
    "grave",            /*   60    */
    "a",                /*   61    */
    "b",                /*   62    */
    "c",                /*   63    */
    "d",                /*   64    */
    "e",                /*   65    */
    "f",                /*   66    */
    "g",                /*   67    */
    "h",                /*   68    */
    "i",                /*   69    */
    "j",                /*   6A    */
    "k",                /*   6B    */
    "l",                /*   6C    */
    "m",                /*   6D    */
    "n",                /*   6E    */
    "o",                /*   6F    */
    "p",                /*   70    */
    "q",                /*   71    */
    "r",                /*   72    */
    "s",                /*   73    */
    "t",                /*   74    */
    "u",                /*   75    */
    "v",                /*   76    */
    "w",                /*   77    */
    "x",                /*   78    */
    "y",                /*   79    */
    "z",                /*   7A    */
    "braceleft",        /*   7B    */
    "bar",              /*   7C    */
    "braceright",       /*   7D    */
    "asciitilde",       /*   7E    */
    "W7f",              /*   7F    */
    "W80",              /*   80    */
    "W81",              /*   81    */
    "quotesinglbase",   /*   82    */
    "florin",           /*   83    */
    "quotedblbase",     /*   84    */
    "ellipsis",         /*   85    */
    "dagger",           /*   86    */
    "daggerdbl",        /*   87    */
    "circumflex",       /*   88    */ /* ?? */
    "perthousand",      /*   89    */
    "Scaron",           /*   8A    */
    "guilsinglleft",    /*   8B    */
    "OE",               /*   8C    */
    "W8d",              /*   8D    */
    "W8e",              /*   8E    */
    "W8f",              /*   8F    */
    "W90",              /*   90    */
    "quoteleft",        /*   91    */
    "quoteright",       /*   92    */
    "quotedblleft",     /*   93    */
    "quotedblright",    /*   94    */
    "bullet",           /*   95    */
    "endash",           /*   96    */
    "emdash",           /*   97    */
    "asciitilde",       /*   98    */  /* ?? */
    "trademark",        /*   99    */
    "scaron",           /*   9A    */
    "guilsinglright",   /*   9B    */
    "oe",               /*   9C    */
    "W9d",              /*   9D    */
    "W9e",              /*   9E    */
    "Ydieresis",        /*   9F    */
    "reqspace",         /*   A0    */ /* ?? */
    "exclamdown",       /*   A1    */
    "cent",             /*   A2    */
    "sterling",         /*   A3    */
    "currency",         /*   A4    */
    "yen",              /*   A5    */
    "brokenbar",        /*   A6    */
    "section",          /*   A7    */
    "dieresis",         /*   A8    */
    "copyright",        /*   A9    */
    "ordfeminine",      /*   AA    */
    "guillemotleft",    /*   AB    */
    "logicalnot",       /*   AC    */
    "sfthyphen",        /*   AD    */
    "registered",       /*   AE    */
    "overstore",        /*   AF    */
    "degree",           /*   B0    */
    "plusminus",        /*   B1    */
    "twosuperior",      /*   B2    */
    "threesuperior",    /*   B3    */
    "acute",            /*   B4    */
    "mu",               /*   B5    */
    "paragraph",        /*   B6    */
    "periodcentered",   /*   B7    */
    "cedilla",          /*   B8    */
    "onesuperior",      /*   B9    */
    "ordmasculine",     /*   BA    */
    "guillemotright",   /*   BB    */
    "onequarter",       /*   BC    */
    "onehalf",          /*   BD    */
    "threequarters",    /*   BE    */
    "questiondown",     /*   BF    */
    "Agrave",           /*   C0    */
    "Aacute",           /*   C1    */
    "Acircumflex",      /*   C2    */
    "Atilde",           /*   C3    */
    "Adieresis",        /*   C4    */
    "Aring",            /*   C5    */
    "AE",               /*   C6    */
    "Ccedilla",         /*   C7    */
    "Egrave",           /*   C8    */
    "Eacute",           /*   C9    */
    "Ecircumflex",      /*   CA    */
    "Edieresis",        /*   CB    */
    "Igrave",           /*   CC    */
    "Iacute",           /*   CD    */
    "Icircumflex",      /*   CE    */
    "Idieresis",        /*   CF    */
    "Eth",              /*   D0    */
    "Ntilde",           /*   D1    */
    "Ograve",           /*   D2    */
    "Oacute",           /*   D3    */
    "Ocircumflex",      /*   D4    */
    "Otilde",           /*   D5    */
    "Odieresis",        /*   D6    */
    "multiply",         /*   D7    */
    "Oslash",           /*   D8    */
    "Ugrave",           /*   D9    */
    "Uacute",           /*   DA    */
    "Ucircumflex",      /*   DB    */
    "Udieresis",        /*   DC    */
    "Yacute",           /*   DD    */
    "Thorn",            /*   DE    */
    "germandbls",       /*   DF    */
    "agrave",           /*   E0    */
    "aacute",           /*   E1    */
    "acircumflex",      /*   E2    */
    "atilde",           /*   E3    */
    "adieresis",        /*   E4    */
    "aring",            /*   E5    */
    "ae",               /*   E6    */
    "ccedilla",         /*   E7    */
    "egrave",           /*   E8    */
    "eacute",           /*   E9    */
    "ecircumflex",      /*   EA    */
    "edieresis",        /*   EB    */
    "igrave",           /*   EC    */
    "iacute",           /*   ED    */
    "icircumflex",      /*   EE    */
    "idieresis",        /*   EF    */
    "eth",              /*   F0    */
    "ntilde",           /*   F1    */
    "ograve",           /*   F2    */
    "oacute",           /*   F3    */
    "ocircumflex",      /*   F4    */
    "otilde",           /*   F5    */
    "odieresis",        /*   F6    */
    "divide",           /*   F7    */
    "oslash",           /*   F8    */
    "ugrave",           /*   F9    */
    "uacute",           /*   FA    */
    "ucircumflex",      /*   FB    */
    "udieresis",        /*   FC    */
    "yacute",           /*   FD    */
    "thorn",            /*   FE    */
    "ydieresis",        /*   FF    */
};
