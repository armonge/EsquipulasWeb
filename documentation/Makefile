chunk:
	xsltproc \
	--output  manual.html \
	/usr/share/xml/docbook/stylesheet/nwalsh5/1.76.1/xhtml-1_1/chunk.xsl \
	help.dbk


pdf:
	xsltproc --output manual.fo \
	/usr/share/xml/docbook/stylesheet/nwalsh5/1.76.1/fo/docbook.xsl \
	help.dbk
	fop  -fo  manual.fo  -pdf  manual.pdf

qch:
	xsltproc \
	--stringparam html.stylesheet  styles.css  \
	--output  manual.html \
	/usr/share/xml/docbook/stylesheet/nwalsh5/1.76.1/xhtml-1_1/chunk.xsl \
	help.dbk


	python qhpgen.py index.html . img styles.css
	qhelpgenerator manual.qhp

