<?php
class UploadsController extends OlogAppController {

   	var $name = 'Uploads';
	var $uses = array();

	function beforeFilter() {
		parent::beforeFilter();
		$this->LogAuth->allowedActions = array('index','proxy');
	}
	
	function index() {
		$id = $this->params['named']['id'];
		$file = $this->params['named']['file'];
		$this->autoRender = false;
		App::import('Vendor','olog.FileUpload',array('file'=>'FileUpload'.DS.'upload.php'));
		App::import('Component', 'CakeSession');
		
		$session = new CakeSession();
		$auth = $session->read('Log');
		if(isset($auth['username'])&&isset($auth['bindPasswd'])){
			$user = $auth['username'];
			$pass = $auth['bindPasswd'];
		}

		$dbinfo = get_class_vars('DATABASE_CONFIG');
		$repository = $dbinfo['olog']['repository'];
		$repoArray = parse_url($repository);
		$base_url = $this->base.'/olog/uploads/';
		
		$options = array(
			'user' => $user,
			'pass' => $pass,
			'id' => $id,
			'repository' => $repository,
			'script_url' => $base_url.'index',
			'upload_dir' => $repoArray['path'],
			'upload_url' => $base_url.'proxy?proxy_url='.rawurlencode($repository),
			'param_name' => 'files',
			// The php.ini settings upload_max_filesize and post_max_size
			// take precedence over the following max_file_size setting:
			'max_file_size' => null,
			'min_file_size' => 1,
			'accept_file_types' => '/.+$/i',
			'max_number_of_files' => null,
			'discard_aborted_uploads' => true,
			'image_versions' => array(
			    'thumbnail' => array(
			        'upload_dir' => $repoArray['path'].'thumbnails/',
			        'upload_url' => $base_url.'proxy?proxy_url='.rawurlencode($repository.'thumbnails/'),
			        'max_width' => 80,
			        'max_height' => 80
			    )
			)
		);
		$upload_handler = new UploadHandler($options);

		switch ($_SERVER['REQUEST_METHOD']) {
		    case 'HEAD':
		    case 'GET':
		        $result = $upload_handler->get($id);
		        break;
		    case 'POST':
		        $result = $upload_handler->post($id);
		        break;
		    case 'DELETE':
		        $result = $upload_handler->delete($id,$file);
		        break;
		    default:
		        $result = header('HTTP/1.0 405 Method Not Allowed');
		}
		return $result;
	}
	
	function proxy() {
		App::import('Vendor','olog.proxy',array('file'=>'proxy'.DS.'class_http.php'));

		$proxy_url = isset($this->params['url']['proxy_url'])?rawurldecode($this->params['url']['proxy_url']):false;
		if (!$proxy_url) {
		    header("HTTP/1.0 400 Bad Request");
		    echo "proxy.php failed because proxy_url parameter is missing";
		    exit();
		}

		// Instantiate the http object used to make the web requests.
		if (!$h = new http()) {
		    header("HTTP/1.0 501 Script Error");
		    echo "proxy.php failed trying to initialize the http object";
		    exit();
		}

		$h->url = $proxy_url;
		$h->postvars = $_POST;

		if (!$h->fetch($h->url)) {
		    header("HTTP/1.0 501 Script Error");
		    echo "proxy.php had an error attempting to query the url";
		    exit();
		}

		// Forward the headers to the client.
		$ary_headers = split("\n", $h->header);
		foreach($ary_headers as $hdr) { header($hdr); }

		// Send the response body to the client.
		echo $h->body;
	}

}
?>