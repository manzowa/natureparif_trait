<?php

namespace Drupal\natureparif_trait\Ajax;

use Drupal\Core\Ajax\CommandInterface;
/**
 * Class ReadMessageReference.
 *
 * @package Drupal\natureparif_trait
 */
class ReadMessageReference implements CommandInterface{

    /**
     * @var  string | object | null, should contains $message 
     */
    protected $message;

    /**
     * Constructor of object ReadMessageReference
     */
    public function __construct($message){
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function render(){
        return array(
           'id' => $this->message->id,
           'content' => $this->message->reference,
        );
    }
}
 