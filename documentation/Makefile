chunk:
	xsltproc \
	stylesheets/xhtml-1_1/chunk.xsl \
	user.dbk

	xsltproc \
	stylesheets/xhtml-1_1/chunk.xsl \
	installation.dbk


pdf:
	xsltproc --output user.fo \
	stylesheets/fo/docbook.xsl \
	user.dbk
	fop  -fo  user.fo  -pdf  user.pdf

	xsltproc --output installation.fo \
	stylesheets/fo/docbook.xsl \
	installation.dbk
	fop  -fo  installation.fo  -pdf  installation.pdf

qch:
	xsltproc \
	--stringparam html.stylesheet  styles.css  \
	stylesheets/xhtml-1_1/chunk.xsl \
	user.dbk


	python qhpgen.py --html . --img img --namespace user.mis.esquipulas.grupoeltriunfo.com.ni --appname "MIS Esquipulas" --title "Manual de usuario de MIS Esquipulas" --toc index.html --other styles.css  > user.qhp
	qhelpgenerator user.qhp



	xsltproc \
	--stringparam html.stylesheet  styles.css  \
	stylesheets/xhtml-1_1/chunk.xsl \
	installation.dbk


	python qhpgen.py --html . --img img --namespace installation.mis.esquipulas.grupoeltriunfo.com.ni --appname "MIS Esquipulas" --title "Manual de instalacion de MIS Esquipulas" --toc index.html --other styles.css > installation.qhp
	qhelpgenerator installation.qhp

	qcollectiongenerator esquipulashelpcollection.qhcp