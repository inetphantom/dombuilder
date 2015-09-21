<?php
class domelement {
	private static $CUSTOMDATA; //PHP can't parse non-trivial expressions in initializers, so initialize at first __get

	public $tagname;
	public $attrs;
	public $content;

	/**
	 *
	 * @param string $elementname
	 */
	function __construct(string $elementname , $content = NULL){
		$customdata = self::$CUSTOMDATA;
		if (!isset($customdata[$elementname])){ //The tag is not in the customizing, so no addidtional data is available
			$this->tagname = $elementname;
			$this->attrs = array();
		}
		else { //The tag exists in the customizing, so it is a custom one, now load the attributes
			$this->attrs = $customdata[$elementname];
			if(isset($customdata[$elementname]['tagname'])){//the element tagname is set properly
				$this->tagname=$customdata[$elementname]['tagname'];

				//remove the tagname form the attributes
				unset($this->attrs['tagname']);
			}
			else{ //the tagname is missing
				//inform the user about his fault
				$message = "Your customize file is not valid.\n $elementname needs a tagname to get created";
				trigger_error($message, E_USER_WARNING);
				//but keep everything running
				$this->tagname=$elementname;
			}
		}
		if (isset ($content)){
			$this->content = $content;
		}
	}
	/**
	 * the toString method gets called if you want to print that object.
	 */
	function __toString(){
		//open the tag
		$string = '<';
		$string.= $this->tagname;
		//add all attributes of the domelement in the opening tag
		foreach ($this->attrs as $key => $value){
			$string .= " $key=\"$value\"";
		}
		$string.= '>\n';

//TODO: 			$string.= '  ';//indent the content

		
		// if there is content, get the string of that one (MAY BE RECURSIVE!)
		if (isset ($this->content)){
			$string.= $this->content->__toString();
		}
		//after the content, close the tag
		$string .= "<\\$this->tagname>";

		return $string;
	}
	public function __call($name, $args){ //if a notexistent mehtod is called, the name gets used as key and the arguments as value to get added to our attributes
		$this->attrs[$name] = $args;
	}
	/**
	 * PHP can't parse non-trivial expressions in initializers, so initialize self::$CUSTOMDATA here
	 */
	static function __init( ){
		if (!isset(self::$CUSTOMDATA)){
			self::$CUSTOMDATA = require_once ('customize.php');
		}
	}
}
domelement::__init();
