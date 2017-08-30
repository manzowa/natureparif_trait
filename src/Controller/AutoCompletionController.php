<?php

namespace Drupal\natureparif_trait\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\natureparif_trait\BaseTraitsStorageInterface;
use Drupal\natureparif_trait\BaseTraitsStorage;
use Drupal\natureparif_trait\BaseTraitsManagerInterface; 
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AutoCompletionController.
 *
 * @package Drupal\natureparif_trait\Controller
 */
class AutoCompletionController extends ControllerBase {

     /**
      *  The active BaserTraitsStorage  
      *  @var Drupal\natureparif_trait\BaseTraitsStorage
      */
     protected $basestorage;

     /**
      *  The active BaseTraitsManager
      *  @var Drupal\natureparif_trait\BaseTraitsManager
      */
     protected $manager;


     /**
      * Constructor 
      */
     public function __construct(BaseTraitsManagerInterface $manager) {
         // $this->basestorage = new BaseTraitsStorage();
         $this->basestorage = \Drupal::service('base_trait.storage');
         $this->manager = $manager;
     }

     /**
      * {@inheritdoc}
      */
     public static function create(ContainerInterface $container) {
       return new static(
         $container->get('base_trait.manager')
      );
    }

    /**
     * Return the familles
     *
     * @return under the form json 
     */
     public function autofamilles(Request $request, $taxonomie){
          $famille=$request->query->get('q');
          return $this->manager->findFamille($famille, $taxonomie);
      }
  
    /**
     * Return the esepces
     * 
     * @return under form json  
     */
    public function autoespeces(Request $request,$taxonomie,$famille){
        $espece=$request->query->get('q');
        return $this->manager->findEspece($espece,$taxonomie, $famille);
    }
    
    /**
     * Return the trait
     * @return under form json  
     */
    public function selectTraitJson($taxonomie,$famille,$espece){
      return $this->manager->findTrait($taxonomie,$famille,$espece);
    }

    /**
     * Return the modalite
     * @return under form json
     */
    public function selectModaliteJson($taxonomie,$famille,$espece,$trait){
         return $this->manager->findModalite($taxonomie,$famille,$espece,$trait);
    }

    /**
     * Return the source
     * @return under form json 
     */
     public function selectSourceJson($taxonomie,$famille,$espece,$trait, $modalite){
         return $this->manager->findSource($taxonomie,$famille,$espece,$trait,$modalite);
     }

}
