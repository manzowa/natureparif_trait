<?php

/**
 * @file
 *
 * @package \Drupal\natureparif_trait\Controller\BaseTraitsController
 */

 namespace Drupal\natureparif_trait\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\natureparif_trait\BaseTraitsStorageInterface;
use Drupal\natureparif_trait\BaseTraitsStorage;
use Drupal\natureparif_trait\BaseTraitsManagerInterface; 
use Drupal\natureparif_trait\BaseTraitsManager;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;




class BaseTraitsController extends ControllerBase{

     /**
      *  The service BaserTraitsStorage  
      *  @var Drupal\natureparif_trait\BaseTraitsStorage
      */
     protected $basestorage;

     /**
      *  The service BaserTraitsManager  
      *  @var Drupal\natureparif_trait\BaserTraitsManagerInterface 
      */
     protected $manager;

     /**
      * Constructor 
      */
     public function __construct(BaseTraitsManagerInterface $manager) {
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
     * A render array representing the description page content.
     *
     * @return array
     */
     public function getContent(){
        return $this->manager->buildContentMajor();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTraits(Request $request,$taxonomie,$famille,$espece,$trait,$modalite,$source){

       // slug 
        $slug_taxonomie = !empty($request->get('group_taxonomique')) ? $request->get('group_taxonomique') : $taxonomie;
        $slug_famille   = !empty($request->get('familles')) ? $request->get('familles') : $famille;
        $piecesEspece = explode("-", $espece);
        $espece = trim($piecesEspece[0]);
        $slug_espece    = $espece;
        $slug_trait     = !empty($request->get('traits')) ? $request->get('traits') : $trait;
        $slug_modalite  = !empty($request->get('modalites')) ? $request->get('modalites') : $modalite;
        $slug_source    = !empty($request->get('sources')) ? $request->get('sources') : $source;

        $build = [];

        $build = $this->manager->buildContentResult($request);
       
         // Recovered the results
        $results = $this->basestorage->FindAll($this->manager->renameTaxonomie($slug_taxonomie),$slug_famille,$slug_espece,$slug_trait, $slug_modalite,$slug_source,$this->manager->buildHeader(),10);
        
         // Generate the table.
        $build['table'] = [
               '#theme' => 'table', 
               '#header' => $this->manager->buildHeader(), 
               '#rows' => $this->manager->buildRow($results), 
        ]; 
        // Finally add the pager.
        $build['pager'] = [
               '#type' => 'pager', 
               '#weight' => 10,
        ];

        return $build;

    }

    /**
     *  Modal Reference
     */
    public function getDetailReferenceById($id){
        $reponse = $this->manager->buildDetailReference($id);
        return $reponse;
    }

    /**
     * Modal trait
     */
    public function  getDetailTraitById($id){
        $reponse = $this->manager->buildDetailTrait($id);
        return $reponse;
    }
	
	/**
     * Modal Modalite
     */
    public function  getDetailModaliteById($id){
        $reponse = $this->manager->buildDetailModalite($id); 
        return $reponse;
    }
}