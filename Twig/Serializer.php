<?php
namespace JMS\SerializerBundle\Twig;

use JMS\SerializerBundle\Serializer\SerializerInterface;


/**
 * Serializer helper twig extension
 * 
 * Basically provides access to JMSSerializer from Twig
 */
class Serializer extends \Twig_Extension {
  protected $serializer;

  public function getName() {
    return 'Serializer';
  }

  public function __construct(SerializerInterface $serializer)
  {
    $this->serializer = $serializer;
  }

  public function getFilters() {
    return array(
      'serialize'      => new \Twig_Filter_Method($this, 'serialize'),
    );
  }

  public function serialize($object, $type = 'json') {
    return $this->serializer->serialize($object, $type);
  }
}
