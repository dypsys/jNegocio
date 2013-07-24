#!/usr/bin/python
import sys, os

source_dir = os.path.realpath(sys.argv[1])
target_dir = os.path.realpath(sys.argv[2])

"""
This function merges the source directory to target directory as symlinks

@Author: Israel D. Canasa (http://israelcanasa.com)
@Parameters:
	source_dir: source directory 
	target_dir: the directory where we'll place the symlinks
@Usage:
    > python symlink.py source_dir target_dir
"""
def create_symlink(source_dir, target_dir):
	paths = os.listdir(source_dir)
	
	for path in paths:
		full_source_path = os.path.join(source_dir, path)
		full_target_path = os.path.join(target_dir, path)
		
		# Ignore symlinks
		if os.path.islink(full_target_path):
			continue
		
		# Ignore hidden files
		if path[0:1] == '.':
			continue
		
		# If path is site, then consider it as root and merge it with the target's first level
		if path == 'site':
			create_symlink(full_source_path, target_dir)
		# If the entity exists already, recursively symlink the two directories
		elif os.path.isdir(full_source_path) and os.path.isdir(full_target_path):
			create_symlink(full_source_path, full_target_path)
		else:
			if os.name == 'nt':
				# Windows style symlinks
				if not os.path.exists(full_target_path):
					opts = '/D' if (os.path.isdir(full_source_path)) else ''
					os.system("mklink %s %s %s" % (opts, full_target_path, full_source_path))
			else:
				# Just print out what's happening
				print "ln -s %s %s" % (full_source_path, full_target_path)
				os.system("ln -s %s %s" % (full_source_path, full_target_path))

create_symlink(source_dir, target_dir)