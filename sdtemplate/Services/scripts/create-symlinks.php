#!/usr/bin/php
<?php

// Configuration.
// Enter directories without trailing slashes.

// The directory with the CVS checkout.
$srcDir = dirname(__FILE__) . '/..';

// The directory where the softlinks are created.
// This is also the directory which you should put in your include path
// after creating the links.
$destDir = '/usr/local/lib/php';

/**
 * This script creates softlinks to the library files you retrieved from
 * the CVS "framework" module.
 *
 * It creates the same directory structure the packages would have if they
 * were installed with "pear install package.xml".
 * For creating this structure it uses the information given in the
 * package.xml files inside each package directory.
 *
 * $Id: create-symlinks.php,v 1.1.1.1 2010/04/15 09:43:04 peimic.comprock Exp $
 *
 * Copyright 2002 Wolfram Kriesing <wolfram@kriesing.de>
 * Copyright 2003-2005 Jan Schneider <jan@horde.org>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author Wolfram Kriesing <wolfram@kriesing.de>
 * @author Jan Schneider <jan@horde.org>
 */

$copy = false;
if (isset($argv)) {
    /* Get rid of the first arg which is the script name. */
    array_shift($argv);
    while ($arg = array_shift($argv)) {
        if ($arg == '--help') {
            print_usage();
        } elseif ($arg == '--copy') {
            $copy = true;
        } elseif (strstr($arg, '--src')) {
            list(,$srcDir) = explode('=', $arg);
        } elseif (strstr($arg, '--dest')) {
            list(,$destDir) = explode('=', $arg);
        } else {
            print_usage("Unrecognised option $arg");
        }
    }
}

@include_once 'Tree/Tree.php';
if (!class_exists('Tree')) {
    exit("You need the PEAR 'Tree' package installed\n");
}

$linker = &new Linker($copy);
$linker->process($srcDir, $destDir);

echo "\n";

//  possible xml-structs
//  <filelist>
//      <dir name="/" baseinstalldir="XML">
//          <file role="php">Parser.php</file>
//          <file role="php" name="RSS.php" />
//      </dir>
//  </filelist>
//
//  <filelist>
//      <file role="php" baseinstalldir="/">DB.php</file>
//      <dir name="DB">
//          <file role="php">common.php</file>
//      </dir>
//  </filelist>
class Linker {

    var $_srcDir;

    var $_baseInstallDir;

    var $_fileroles = array('php');

    var $_role;

    var $_copy;

    function Linker($copy = false)
    {
        $this->_copy = $copy;
    }

    function process($srcDir, $destDir)
    {
        $this->_srcDir = $srcDir;
        $packageFile = $this->_srcDir . '/package.xml';

        if (!is_file($packageFile)) {
            echo "No package.xml in $this->_srcDir\n";
            return false;
        }

        $tree = Tree::setupMemory('XML', $packageFile);
        $tree->setup();

        // read package name
        $packageName = trim($tree->getElementContent('/package/name', 'cdata'));
        echo "Processing package $packageName.\n";

        // look for filelist in '/package/release/filelist'
        $filelist = $tree->getElementByPath('/package/release/filelist');

        if ($filelist) {
            // do this better, make the tree class work case insensitive
            $baseInstallDir = $filelist['child']['attributes']['baseinstalldir'];

            $this->_baseInstallDir = $destDir;
            if ($baseInstallDir != '/') {
                $this->_baseInstallDir .= '/' . $baseInstallDir;
            }

            if (!is_dir($this->_baseInstallDir)) {
                require_once 'System.php';
                System::mkdir('-p ' . $this->_baseInstallDir);
            }

            $this->_handleFilelistTag($filelist);
        } else {
            echo "No filelist tag found inside: $packageFile\n";
        }
    }

    function _handleFilelistTag($element, $curDir = '')
    {
        foreach ($element['children'] as $child) {
            switch ($child['name']) {
            case 'file':
                $this->_handleFileTag($child, $curDir);
                break;
            case 'dir':
                $this->_handleDirTag($child, $curDir);
                break;
            default:
                echo "No handler for tag: ${child['name']}\n";
                break;
            }
        }

    }

    function _handleDirTag($element, $curDir)
    {
        if ($element['attributes']['name'] != '/') {
            if (substr($curDir, -1) != '/') {
                $curDir = $curDir . '/';
            }
            $curDir = $curDir . $element['attributes']['name'];
        }

        if (!empty($element['attributes']['role'])) {
            $this->_role = $element['attributes']['role'];
        }

        if (!is_dir($this->_baseInstallDir . $curDir)) {
            require_once 'System.php';
            System::mkdir('-p ' . $this->_baseInstallDir . $curDir);
        }

        $this->_handleFilelistTag($element, $curDir);
    }

    function _handleFileTag($element, $dir)
    {
        if (!empty($element['attributes']['role'])) {
            $this->_role = $element['attributes']['role'];
        }

        if (!in_array($this->_role, $this->_fileroles)) {
            return;
        }

        if (!empty($element['attributes']['name'])) {
            $filename = $element['attributes']['name'];
        } else {
            $filename = $element['cdata'];
        }
        $filename = trim($filename);

        if ($this->_copy) {
            $cmd = "cp {$this->_srcDir}$dir/$filename {$this->_baseInstallDir}$dir/$filename";
        } else {
            $cmd = "ln -sf {$this->_srcDir}$dir/$filename {$this->_baseInstallDir}$dir/$filename";
        }
        exec($cmd);
    }

}

function print_usage($message = '') {

    if (!empty($message)) {
        print "create-symlinks.php: $message\n\n";
    }

    print <<<USAGE
Usage: create-symlinks.php [OPTION]

Possible options:
  --copy        Do not create symbolic links, but actually copy the libraries.
  --src=DIR     The source directory for the framework libraries.
  --dest=DIR    The destination directory for the framework libraries.

USAGE;
    exit;
}
