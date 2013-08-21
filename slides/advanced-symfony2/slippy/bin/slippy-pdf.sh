#!/bin/sh
if [ "" = "$1" ]; then
    echo "Usage: ./phantom-pdf.sh <slides URL> <PDF filename>"
    exit 1
fi
if [ "" = "$2" ]; then
    echo "Usage: ./phantom-pdf.sh <slides URL> <PDF filename>"
    exit 1
fi

mkdir -p tmp-pdf/
rm -f tmp-pdf/*.png
rm -f tmp-pdf/*.pdf
rm -f tmp-pdf/*.html
phantom=`which phantomjs 2>/dev/null`
if [ "" = "$phantom" ]; then
    if [ -f './phantomjs/phantomjs' ]; then
        phantom='./phantomjs/phantomjs'
        bin='.'
    elif [ -f './bin/phantomjs/phantomjs' ]; then
        phantom='./bin/phantomjs/phantomjs'
        bin='./bin'
    else
        echo 'phantomjs could not be found, either download it and put it inside a phantomjs directory, or make it accessible through your PATH environment variable.'
        exit
    fi
fi

pdftk=`which pdftk 2>/dev/null`
if [ "" = "$pdftk" ]; then
    if [ -f "$bin/pdftk/pdftk" ]; then
        pdftk="$bin/pdftk/pdftk"
    else
        echo 'pdftk could not be found, either download it and put it inside a pdftk directory, or make it accessible through your PATH environment variable.'
        exit
    fi
fi

$phantom $bin/phantom-slippy-to-pdf.js "$1" tmp-pdf/
if [ "$?" != "0" ]
then
    echo 'PhantomJS error, aborting.'
    exit
fi

$pdftk tmp-pdf/*.pdf cat output "$2"
rm -r tmp-pdf/
