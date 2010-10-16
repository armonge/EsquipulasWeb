# -*- coding: utf-8 -*-
from PyQt4.QtCore import QCoreApplication
from PyQt4.QtSql import QSqlDatabase, QSqlQuery
import sys
import urllib
import json
import datetime
from optparse import OptionParser
if __name__ == "__main__":
    app = QCoreApplication( sys.argv )
    
    parser = OptionParser()
    parser.add_option("-m", "--month", dest="month",
                  help="El mes a descargar")
    parser.add_option("-y", "--year", dest="year",
                  help=u"El año a descargar")
    
    (options, args) = parser.parse_args()
    
    

    db = QSqlDatabase.addDatabase( "QMYSQL" )
    db.setDatabaseName( "esquipulasdb" )
    db.setUserName( "root" )
    db.setHostName( "localhost" )
    db.setPassword( "root" )

    query = QSqlQuery()
    try:
        
        year = int(options.year if not options.year is None else datetime.date.today().year)
        month = int(options.month if not options.month is None else datetime.date.today().month)
        url = "http://www.elpueblopresidente.com/servicios/wsmoneda.php?formato=jsonvalido&ano=%d&mes=%d" % ( year, month )
        print url
        file = urllib.urlopen( url )
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
        print query.lastError().text()
        print unicode(inst)
    finally:
        if db.isOpen():
            db.close()

    app.exit()



