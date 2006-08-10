<?php
/**
 * Context Handler Class  - ContextHandler.class.php
 * 
 * Handles the Context. It is this responsability to check if the context file
 * exists if not create it. But the main responsability is store from and 
 * restore to this file. 
 * 
 * PHP version 5
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 * 
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * This product includes software developed by the Fusebox Corporation 
 * (http://www.fusebox.org/).
 * 
 * The Original Code is Fuses "a Candango implementation of Fusebox Corporation 
 * Fusebox" part .
 * 
 * The Initial Developer of the Original Code is Flávio Gonçalves Garcia.
 * Portions created by Flávio Gonçalves Garcia are Copyright (C) 2005 - 2006.
 * All Rights Reserved.
 * 
 * Contributor(s): Flávio Gonçalves Garcia.
 *
 * @category   controller
 * @package    myfuses.context
 * @author     Flávio Gonçalves Garcia <fpiraz@gmail.com>
 * @copyright  Copyright (c) 2005 - 2006 Candango Group <http://www.candango.org/>
 * @license    http://www.mozilla.org/MPL/MPL-1.1.html  MPL 1.1
 * @version    SVN: $Id$
 * @link       *
 * @see        *
 * @since      Release 1
 * @deprecated *
 */

/**
 * Context Handler Class  - ContextHandler.class.php
 * 
 * Handles the Context. It is this responsability to check if the context file
 * exists if not create it. But the main responsability is store from and 
 * restore to this file. 
 * 
 * PHP version 5
 *
 * @category   controller
 * @package    myfuses.context
 * @author     Flávio Gonçalves Garcia <fpiraz@gmail.com>
 * @copyright  Copyright (c) 2005 - 2006 Candango Group <http://www.candango.org/>
 * @license    http://www.mozilla.org/MPL/MPL-1.1.html  MPL 1.1
 * @version    SVN: $Release: Fusebox.class.php 60 2006-04-11 18:20:24Z piraz $
 * @link       *
 * @see        *
 * @since      Release 1
 * @deprecated *
 * @abstract
 */
class ContextHandler {
    
    private static $path = null;
    
    /**
     * Initializes the context
     * 
     * @access public
     */
    public static function initContext() {
    	
        $contextCacheFile = "";
        
        if ( is_file( self::getFile() ) ) {
            self::restoreContext();
        }
        else {
            self::storeContext();
        }
        
    }
    
    /**
     * Stores the context into a file
     * 
     * @access public
     */
    public static function storeContext() {
    	
        $context = Context::getInstance();
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<context>\n";
        
        $xml .= self::getApplicationsXML( $context );
        
        $xml .= "</context>";
        
        if( !$fp = fopen( self::getFile(),"w" ) ){
        	die("Could not open Parsed File file");
        }
        
        if ( !flock($fp,LOCK_EX) ) {
            die("Could not get exclusive lock to Parsed File file");
        }
        
        if ( !fwrite($fp, $xml ) ) {
           var_dump( "deu pau 2!!!" );
        }
        
        flock($fp,LOCK_UN);
        
        fclose($fp);
    }
    
    public static function getApplicationsXML( &$context ) {
    	// writing applications
        $xml = "\t<applications>\n";
        foreach( $context->getApplications() as $application ) {
            $xml .= $application->getContextXML( "\t\t" );
        }
        $xml .= "\t</applications>\n";
        return $xml;
    }
    
    /**
     * Restores the context from file
     * 
     * @access public
     */
    public static function restoreContext() {
    	
        if( !$fp = fopen( self::getFile(), "r" ) ){
            die("Could not open Parsed File file");
        }
        
        if ( !flock( $fp, LOCK_EX ) ) {
            die("Could not get exclusive lock to Parsed File file");
        }
        
        $xmlString = fread( $fp, filesize( self::getFile() ) ); 
        
        
        $simpleXML = new SimpleXMLElement( $xmlString );
        
        $context = Context::getInstance();
        
        $applicationsXML = &$simpleXML->applications;
        
        foreach( $applicationsXML->application as $applicationItem ) {
        	$application = new Application();
            $application->setAttributesFromContext( $applicationItem );
            $context->addApplication( $application );
        }
        
    }
    
    public static function getPath(){
    	return self::$path;
    }
    
    public static function setPath( $path ){
        return self::$path = $path;
    }
    
    /**
     * Returns the file store file name
     * 
     * @access public
     */
    public static function getFile() {
        if ( is_null( self::getPath() ) ) {
        	return MyFuses::ROOT_PATH . "store" . 
                DIRECTORY_SEPARATOR . Context::getInstance()->getName()
                . ".xml";
        }
        else {
        	return self::getPath() . "store" . 
                DIRECTORY_SEPARATOR . Context::getInstance()->getName()
                . ".xml";
        }
    }
    
    /**
     * Registers one application in context
     * 
     * @param Application application The Application
     * @access public
     */
    public static function registerApplication( Application $application ) {
    	$context = Context::getInstance();
        $application->setRegisterTime( time() );
        $context->addApplication( $application );
        
        self::storeContext();
    }
    
}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */