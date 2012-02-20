<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yiqing
 * Date: 12-2-17
 * Time: 下午10:33
 * To change this template use File | Settings | File Templates.
 * @see https://github.com/aehlke/tag-it
 * --------------------------------------------------------------
 * there are lots of jquery tag plugins i  just chose a random one
 * this is also my candidate one : http://xoxco.com/projects/code/tagsinput/(https://github.com/xoxco/jQuery-Tags-Input)
 *
 * here you can find more :
 * (http://roberto.open-lab.com/2010/02/10/a-delicious-javascript-tagging-input-field/)collections
 * http://www.fatihkadirakin.com/dev/jquerytag/
 * http://blog.crazybeavers.se/wp-content/Demos/jquery.tag.editor/
 * http://superdit.com/2011/03/12/create-delicious-tag-field-using-jquery/
 * http://plugins.jquery.com/project/tagger
 * http://plugins.jquery.com/project/ptags
 * --------------------------------------------------------------
 */
class JTagIt extends CWidget
{
    /**
     * @static
     * @param bool $hashByName
     * @return string
     * return this widget assetsUrl
     */
    public static function getAssetsUrl($hashByName = false)
    {
        // return CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName);
        return Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName, -1, YII_DEBUG);
    }

    /**
     * @var string
     * ----------------------------
     * available themes see:
     * http://jqueryui.com/download
     * vader|humanity|eggplant|...
     * ----------------------------
     */
    public $theme = 'flick';
    /**
     * @var bool
     * -------------------
     * you want use local theme css
     *
     * not supported yet
     * -------------------
     */
    public $themeLocal = false;

    /**
     * @var string
     */
    public  $baseUrl ;

    /**
     * @var bool
     */
    public $debug  ;

    /**
     * @var \CClientScript
     */
   protected $cs ;

    /**
     * @var array|string
     * -------------------------
     * the options will be passed to the underlying plugin
     *   eg:  js:{key:val,k2:v2...}
     *   array('key'=>$val,'k'=>v2);
     * -------------------------
     */
    public $options = array();


    /**
     * @var string
     */
    public $selector = '';

    /***
     * @return JTagIt
     */
    public function publishAssets()
    {
        if(empty($this->baseUrl)){
            $assetsDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
            $this->baseUrl = Yii::app()->getAssetManager()->publish($assetsDir, false, -1, $this->debug);
        }
        return $this;
    }

    /**
     * @return JTagIt
     */
    public function registerClientScripts(){
      $this->cs->registerCoreScript('jquery')
          ->registerCoreScript('jquery.ui') //see system.web.js.packages.php to learn about what name you can use ^-^
        ->registerScriptFile($this->baseUrl.'/js/tag-it.js');
        //register the css file
        $this->cs->registerCssFile( $this->baseUrl.'/css/jquery.tagit.css')
            ->registerCssFile('http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/'.$this->theme .'/jquery-ui.css');

        return $this;
    }

    public function init(){

        parent::init();
        if (!isset($this->debug)) {
            $this->debug = defined(YII_DEBUG) ? YII_DEBUG : true;
        }
        $this->cs = Yii::app()->getClientScript();
        // publish assets and register css/js files
        $this->publishAssets();
        $this->registerClientScripts();

        $options = empty($this->options)? '' : CJavaScript::encode($this->options);

        $jsSetup = <<<JS_INIT
           $("{$this->selector}").tagit({$options});
JS_INIT;

        $this->cs->registerScript(__CLASS__.'#'.$this->getId(),$jsSetup,CClientScript::POS_READY);
    }


    public function run(){

    }


    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        try {
            //shouldn't swallow the parent ' __set operation
            parent::__set($name, $value);
        } catch (Exception $e) {
            $this->options[$name] = $value;
        }
    }
}
