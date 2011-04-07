#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
#
#       Copyright 2010 Andrés Reyes Monge <armonge@gmail.com>
#
#       This program is free software; you can redistribute it and/or modify
#       it under the terms of the GNU General Public License as published by
#       the Free Software Foundation; either version 2 of the License, or
#       (at your option) any later version.
#
#       This program is distributed in the hope that it will be useful,
#       but WITHOUT ANY WARRANTY; without even the implied warranty of
#       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#       GNU General Public License for more details.
#
#       You should have received a copy of the GNU General Public License
#       along with this program; if not, write to the Free Software
#       Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
#       MA 02110-1301, USA.

import sys
from BeautifulSoup import BeautifulSoup


class QHPWriter(object):
	'''
	Esta clase genera un archivo qhp
	'''
	header = '<?xml version="1.0" encoding="UTF-8"?>\n'
	header += '<QtHelpProject version="1.0">\n'

	namespace = ''
	virtual_folder = ''
	files = []
	toc = []
	app_name = ''
	doc_title = ''
	
	def _generate_toc(self):
		toc = u''
		toc += '<section title="' + self.doc_title + '" ref="index.html">'
		for chapter in self.toc:
			toc += '\t\t<section title="' + chapter['title'] + '" ref="' + chapter['link'] + '" >\n'

			for section in chapter['sections']:
				toc += '\t\t\t<section title="' + section['title'] + '" ref="' + section['link'] + '" />\n'

			toc += '\t\t</section>\n'
		toc += '</section>'
		return toc

	def _generate_file_list(self):
		file_list = ''
		for f in self.files:
			file_list += '\t\t<file>' + f + '</file>\n'

		return file_list
			

	def write(self, dest = None):
		document = u''
		document += self.header
		document += '<namespace>' + self.namespace + '</namespace>\n'
		document += '<virtualFolder>\n' + self.virtual_folder + '</virtualFolder>\n'
		document += '<filterSection>\n'
		document += '<filterAttribute>' + self.app_name + '</filterAttribute>'
		document += '\t<toc>\n' + self._generate_toc() + '\t</toc>\n'
		document += '\t<files>\n' + self._generate_file_list() + '</files>\n'
		document += '</filterSection>\n'
		document += '</QtHelpProject>\n'

		return document.encode('utf-8')


def get_chapters( html_file ):
	#open index for traversal
	html_file = open(html_file)
	soup = BeautifulSoup( html_file.read())
	toc = soup.find('div','toc')

	chapters = []
	#get chapters
	for dt in toc.dl.findAll('dt', recursive=False):
		chapter =  dt.find('a')
		d = {
			'title' :  chapter.string.replace('\t','').replace('\n',''),
			'link' :  chapter['href'],
			'sections' : []
		}

		#get sections
		dd = dt.findNextSibling('dd')
		if not dd is None:
			for section in dd.findAll('a'):
				d['sections'].append({
					'title' : section.string.replace('\t','').replace('\n',''),
					'link': section['href']
				})
		chapters.append(d)

	return chapters

def get_files(html_directory, image_directory, other = None):
	'''
	@param image_directory: El directorio donde se encuentran las imagenes
	@type image_directory: str

	@param other: Una lista de archivos extra que se deberian de agregar
	@type other: list
	'''
	if not other is None:
		file_list = other
	else:
		file_list = []
	import os
	for f in os.listdir(image_directory):
		if f[-3:] == 'png':
			file_list.append( os.path.join(image_directory, f) )

	for f in os.listdir(html_directory):
		if f[-4:] == 'html':
			file_list.append( os.path.join(html_directory, f) )
				
	return file_list




if __name__ == '__main__':
	import argparse

	parser = argparse.ArgumentParser(description='Generar un archivo qhp')
	parser.add_argument('--html', help='El directorio en donde se encuentran los archivos html')
	parser.add_argument('--img', help='El directorio en donde se encuentran los archivos png')
	parser.add_argument('--other', help='Una lista de otros archivos que se deban de incluir', nargs='+')
	parser.add_argument('--namespace', help ='El namespace del documento')
	parser.add_argument('--appname', help=u'El nombre de la aplicación', type=unicode)
	parser.add_argument('--title', help='El nombre del documento', type=unicode)
	parser.add_argument('--toc', help='El nombre del archivo html en donde se encuentra la TOC')

	args = parser.parse_args()

	qhp_writer = QHPWriter()
	qhp_writer.toc = get_chapters(args.toc)
	qhp_writer.files = get_files(args.html, args.img, args.other)
	qhp_writer.namespace = args.namespace
	qhp_writer.app_name = args.appname

	qhp_writer.doc_title = args.title 
	print qhp_writer.write()