<?php

class SendGrid {
  const VERSION = '3.0.0';

  protected $namespace  = 'SendGrid',
            $headers    = array('Content-Type' => 'application/json'),
            $client,
            $options;
  public    $api_user,
            $api_key,
            $url,
            $endpoint,
            $version = self::VERSION;

  
  public function __construct($api_user, $api_key, $options=array()) {
    $this->api_user = $api_user;
    $this->api_key = $api_key;

    $options['turn_off_ssl_verification'] = (isset($options['turn_off_ssl_verification']) && $options['turn_off_ssl_verification'] == true);
    $protocol = isset($options['protocol']) ? $options['protocol'] : 'https';
    $host = isset($options['host']) ? $options['host'] : 'api.sendgrid.com';
    $port = isset($options['port']) ? $options['port'] : '';
    $this->options  = $options;

    $this->url = isset($options['url']) ? $options['url'] : $protocol . '://' . $host . ($port ? ':' . $port : '');
    $this->endpoint = isset($options['endpoint']) ? $options['endpoint'] : '/api/mail.send.json';

    $this->client = new \TinyHttp($this->url, array('curlopts' => array(CURLOPT_USERAGENT => 'sendgrid/' . $this->version . ';php',
      CURLOPT_SSL_VERIFYPEER => !$this->options['turn_off_ssl_verification'])));
  }

  /**
   * @return array The protected options array
   */
  public function getOptions() {
    return $this->options;
  }

  /**
   * Makes a post request to SendGrid to send an email
   * @param SendGrid\Email $email Email object built
   * @throws SendGrid\Exception if the response code is not 200
   * @return stdClass SendGrid response object
   */
  public function send(SendGrid\Email $email) {
    $form             = $email->toWebFormat();
    $form['api_user'] = $this->api_user; 
    $form['api_key']  = $this->api_key; 

    $response = $this->postRequest($this->endpoint, $form);

    if ($response->code != 200) {
      throw new SendGrid\Exception($response->raw_body, $response->code);
    }

    return $response;
  }

  /**
   * Makes the actual HTTP request to SendGrid
   * @param $endpoint string endpoint to post to
   * @param $form array web ready version of SendGrid\Email
   * @return SendGrid\Response
   */
  public function postRequest($endpoint, $form) {
    $res = $this->client->post($endpoint, null, $form);

    $response = new SendGrid\Response($res->code, $res->headers, $res->body, json_decode($res->body));

    return $response;
  }

  public static function register_autoloader() {
    spl_autoload_register(array('SendGrid', 'autoloader'));
  }

  public static function autoloader($class) {
    // Check that the class starts with 'SendGrid'
    if ($class == 'SendGrid' || stripos($class, 'SendGrid\\') === 0) {
      $file = str_replace('\\', '/', $class);

      if (file_exists(dirname(__FILE__) . '/' . $file . '.php')) {
        require_once(dirname(__FILE__) . '/' . $file . '.php');
      }
    }
  }
}
