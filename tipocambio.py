# -*- coding: utf-8 -*-
from PyQt4.QtCore import QCoreApplication
from PyQt4.QtSql import QSqlDatabase, QSqlQuery
import sys
import urllib
import json
import datetime
if __name__ == "__main__":
    app = QCoreApplication( sys.argv )


    db = QSqlDatabase.addDatabase( "QMYSQL" )
    db.setDatabaseName( "esquipulasdb" )
    db.setUserName( "root" )
    db.setHostName( "localhost" )
    db.setPassword( "root" )

    query = QSqlQuery()
    try:
	file = urllib.urlopen( "http://www.elpueblopresidente.com/servicios/wsmoneda.php?formato=jsonvalido&ano=%d&mes=%d" % ( datetime.date.today().year, datetime.date.today().month ) )
	data = json.loads( file.read() )

	if not db.open():
	    raise Exception( u"No se pudo abrir la conexión a la base de datos" )

	if not db.transaction():
	    raise Exception( u"No se pudo iniciar la transacción" )



	for record in data['tipodecambioni']:

	    if not query.prepare( """
	    INSERT INTO tiposcambio (tasa, fecha) VALUES ( :tasa, :fecha)
	    """ ):
		raise Exception( "No se pudo preparar la consulta" )
	    query.bindValue( ":tasa", record['cambio']['valor'] )
	    query.bindValue( ":fecha", record['cambio']['fecha'] )
	    print "*************************"
	    print record['cambio']['valor']
	    print record['cambio']['fecha']
	    print "*************************"
	    if not query.exec_():
		raise Exception( "No se pudo ejecutar la consulta" )

	if not db.commit():
	    raise Exception( "No se pudo completar la transaccion" )

    except Exception as inst:
	db.rollback()
	print query.lastError().databaseText()
	print inst
    finally:
	if db.isOpen():
	    db.close()

    app.exit()



