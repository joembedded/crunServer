echo "Make CrunServer Distribution Script 03.02.2025 (C)JoEmbedded"
rem --------------------------------------------------------------------
rem Dieses Script erzeugt die Distributions-Version. 
rem Benoetigt dazu wird PHP und ein Minifier 
rem Hier verwendet: uglify-js V3.19.3:
rem https://www.npmjs.com/package/uglify-js / npm install uglify-js -g
rem 
rem Wichtig: Fuer Distribution in index.html window.jdDebug auf 0 setzen!
rem --------------------------------------------------------------------

@echo off
md ..\appdist
@echo on

php ltxcopy.php ../appdev  ../appdist -tBLE

rem *todo* echo "uglify... "

rem *todo* call uglifyjs --warn ../appdev/js/blx.js -m -c -o ../appdist/js/blx.js
rem *todo* call uglifyjs --warn ../appdev/js/blStore.js -m -c -o ../appdist/js/blStore.js
rem *todo* call uglifyjs --warn ../appdev/js/blxdash.js -m -c -o ../appdist/js/blxdash.js
rem *todo* call uglifyjs --warn ../appdev/js/intmain_i18n.js -m -c -o ../appdist/js/intmain_i18n.js
rem *todo* call uglifyjs --warn ../appdev/js/jodash.js -m -c -o ../appdist/js/jodash.js
rem *todo* call uglifyjs --warn ../appdev/js/qrscanner.js -m -c -o ../appdist/js/qrscanner.js
rem *todo* call uglifyjs --warn ../appdev/js/FileSaver.js -m -c -o ../appdist/js/FileSaver.js


pause 