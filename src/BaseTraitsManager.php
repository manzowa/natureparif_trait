<?php

namespace Drupal\natureparif_trait;

/**
 * Class BaseTraitsManager.
 *
 * @package Drupal\natureparif_trait
 */
use Drupal\natureparif_trait\BaseTraitsStorageInterface ;
use Drupal\natureparif_trait\BaseTraitsManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;


class BaseTraitsManager implements BaseTraitsManagerInterface {
  /**
   * The base de trait service
   * @var \Drupal\natureparif_trait\BaseTraitsStorageInterface 
   */
  protected $basestorage;
   
  /**
   * Construtor a BaseTraitsManager object    
   * @param \Drupal\natureparif_trait\BaseTraitsStorageInterface 
   */
  public function __construct(BaseTraitsStorageInterface $basestorage) {
    $this->basestorage = $basestorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
       return new static(
         $container->get('base_trait.storage')
      );
   }

  /**
   * {@inheritdoc}
   */
  public function buildContentMajor(){
    // Build the form for the nice output.
       $content['form'] = [
           '#type' => 'form',
            'form' =>\Drupal::formBuilder()->getForm('Drupal\natureparif_trait\Form\BaseTraitsSearchForm'),
            '#form_id' => 'basestorages_search_form',
            '#description' => 'The description of page form',
            '#attributes' => [],
        ]; 

        // Build the info page for the nice output.
        $content['base_info'] = [
            '#theme' => 'natureparif_trait',
            '#description' => 'The description of page trait',
            '#attributes' => [],
        ];
        $content['#attached']['library'][]= 'natureparif_trait/natureparif-trait.style';
        return $content;
   }

  /**
   * {@inheritdoc}
   */
  public function buildContentResult($request){

        // Get All variables in PUT.
        $current_path = \Drupal::service('path.current')->getPath();
        $new_url = explode('/', $current_path);
        $new_taxonomie = $new_url[3];
        $new_famille = $new_url[4];
        $new_espece = $new_url[5];
        $new_trait = $new_url[6];
        $new_modalite = $new_url[7];
        $new_source = $new_url[8];
        $new_array = [$new_taxonomie, $new_famille,$new_espece, $new_trait,$new_modalite, $new_source];
       
        $build = [];
        // Build the form for the nice output.
        $build['form'] = [
               '#type' => 'form',
               'form' =>\Drupal::formBuilder()->getForm('Drupal\natureparif_trait\Form\BaseTraitsSearchForm'),
               '#form_id' => 'basestorages_search_form',
               '#description' => 'The description of page form',
               '#attributes' => [],
         ]; 

        $build['export_link'] = [
               '#title' => t('Export Excel'),
               '#type' => 'link',
               '#attributes' => [
                   'class' => array('trait-export'),
                ],
               '#url' => Url::fromRoute('natureparif_trait.export_excel',
                    ['taxonomie'=>$new_taxonomie,'famille'=>$new_famille,'espece'=>$new_espece,'trait'=>$new_trait,'modalite'=>$new_modalite,'source'=>$new_source, ]
                ),
        ];
        $build['#attached']['library'][]= 'natureparif_trait/natureparif-trait.style';
        return $build;
  }

  
  /**
   * build header 
   * 
   * @return array header
   */
  public function buildHeader(){
       $header = [ 
            array('data'=> t('Groupe Taxonomique'),'field'=>'rt.grp'),
            array('data'=> t('Famille'),'field'=>'rtc.Famille'),
            array('data'=> t('Cd_nom'),'field'=>'tem.cd_nom'),
            array('data'=> t('Nom scientifique'),'field'=>'rt.LB_NOM'),
            array('data'=> t('Nom vernaculaire'),'field'=>'rt.NOM_VERN'),
            array('data'=> t('Trait'),'field'=>'tt.libelle'),
            array('data'=> t('Modalité'),'field'=>'tm.libelle'),
            array('data'=> t('Source'),'field'=>'ts.libelle'),
            array('data'=> t('Référence'),'field'=>'tr.libelle'),
            array('data'=> t('Utilisateur'),'field'=>'user.name'), 
        ];
      return $header;
   }

  /**
   * build Row 
   * 
   * @return Row 
   */
  public function buildRow($results = []){
      $rows = [];
      foreach($results as $row){
            $urlR = Url::fromRoute('natureparif_trait.modal.reference', array('id' => $row->reference_id));
            $urlR->setOption('attributes', ['class' => ['use-ajax'], 'data-dialog-type' =>'modal','data-dialog-options' => json_encode(array(
                'width' => 600,
                 'height' => 200,
             ))]);
            $urlT =  Url::fromRoute('natureparif_trait.modal.trait', array('id' => $row->trait_id));
            $urlT->setOption('attributes', ['class' => ['use-ajax'], 'data-dialog-type' =>'modal','data-dialog-options' => json_encode(array(
                'width' => 600,
                 'height' => 200,
             ))]);
			 
			$urlM =  Url::fromRoute('natureparif_trait.modal.modalite', array('id'=> $row->modalite_id));
            $urlM->setOption('attributes', ['class' => ['use-ajax'], 'data-dialog-type' =>'modal','data-dialog-options' => json_encode(array(
                'width' => 600,
                 'height' => 200,
             ))]); 
            $urlE =  Url::fromUri(self::genere_url($row->grp,$row->cd_nom,$row->LB_NOM ));
   
            $rows[] = array(
               'data' => array( 
                      'grp' => t($this->renameTaxonomie($row->grp)),
                      'FAMILLE'=>t($row->FAMILLE),
                      'cd_nom' =>t($row->cd_nom),
                      'LB_NOM' => \Drupal::l(substr(t($row->LB_NOM),0,20),$urlE), 
                      'NOM_VERN'=>substr(t($row->NOM_VERN),0,20),
                      'Trait' =>\Drupal::l(substr(t($row->trait),0,20),$urlT),
                      'modalite' =>\Drupal::l(substr(t($row->modalite),0,20),$urlM),
                      'source'=> t($row->source),
                      'reference'=>\Drupal::l(substr(t($row->reference),0,20),$urlR),
                      'name' =>t($row->name),
                ),
            ); 
        }
        return $rows;
   }

   /**
    * {@inheritdoc}
    */
  public function findTaxonomies(){
       $taxonomies = [];
       $results = $this->basestorage->getAllTaxonomies();  
       foreach($results as $row){
           if(!empty($row->grp)){
              $taxonomies[$this->renameTaxonomie($row->grp)] = $this->renameTaxonomie($row->grp);
           }
       }
       $taxonomies  = array_unique($taxonomies , SORT_REGULAR); //  for doublons
       return $taxonomies;
   }

   /**
    * {@inheritdoc}
    */
   public function findValuesTaxonomies(){
       $taxonomies = [];
       $resultArray = [];
       $results = $this->basestorage->getAllTaxonomies();

       foreach($results as $row){
           $resultArray [] = $row->grp;
       }
       $resultArray  = array_unique($resultArray , SORT_REGULAR); // Not Doublons
       foreach($resultArray as $item){
           if(!empty($item)){
              $taxonomies[] = ['value' => $this->renameTaxonomie($item)];
           }
       }
      return $taxonomies;
   }

   /** 
     * falcultatif
     */
    public function buildTrait(){
        $traits = [];
         $results = $this->basestorage->getTrait(); 
         foreach($results as $row){
           if(!empty($row->trait)){
             $traits[$row->trait] = $row->trait;
           }
         } 
         $traits  = array_unique($traits , SORT_REGULAR); //  for doublons
         return $traits;  
    }

   /**
    * {@inheritdoc}
    */
    public function testfindFamille($famille, $taxonomie ="plantae"){
        if(!empty($famille) && strlen($famille)>0){
             $results = $this->basestorage->getFamille($famille,$this->renameTaxonomie($taxonomie));
             foreach($results as $result){
                $familles[] =  $result->Famille;
             } 
        }
        return new JsonResponse($familles); 
    }

    /**
     * {@inheritdoc}
     */
    public function findFamille($famille, $taxonomie){
        $familles = [];
        $results  = null;
        
        if((!empty($famille) && strlen($famille)>0)){
             $results = $this->basestorage->getfamille($famille,$this->renameTaxonomie($taxonomie));
             if(!empty($results)){
                foreach($results as $result){
                    $familles[] =  $result->FAMILLE;
                }  
             }else{
                 $familles[] = "Non results";
             } 
        }
        return new JsonResponse($familles); 
    }
    
    /**
     * {@inheritdoc}
     */
    public function findEspece($espece, $taxonomie,$famille){
       $especes = [];
       $piecesEspece = explode("-", $espece);
       $espece = trim($piecesEspece[0]);
       if(!empty($espece) && strlen($espece)>0){
           $results = $this->basestorage->getEspece($espece,$this->renameTaxonomie($taxonomie),$famille);
            if(!empty($results)){
               foreach($results as $item){ 
                 $expresion = $item->LB_NOM."-".$item->expression;
                 $especes[] = $expresion;
               }
            }else{
                 $especes[] = "Non results";
            }
       }
       return new JsonResponse($especes); 
    }

    /**
     * {@inheritdoc}
     */
    public function findTrait($taxonomie,$famille,$espece){
        $traits = [];
        $piecesEspece = explode("-", $espece);
        $espece = trim($piecesEspece[0]); 
        $taxonomie = trim($this->renameTaxonomie($taxonomie));
        $results = $this->basestorage->getTrait($taxonomie,$famille,$espece);
        foreach($results as $result){ 
            $traits[] = $result->trait;
        }
        $traits  = array_unique($traits,SORT_REGULAR); //  for doublons
        return new JsonResponse($traits); 
     }

    /**
     * {@inheritdoc}
     */
    public function findModalite($taxonomie,$famille,$espece,$trait){
        $modalites = [];
        $piecesEspece = explode("-", $espece);
        $espece = trim($piecesEspece[0]);

        $results = $this->basestorage->getModalite($this->renameTaxonomie($taxonomie),$famille,$espece,$trait);   
        foreach($results as $row){
            $modalites[] = $row->modalite;
         } 
         $modalites  = array_unique($modalites , SORT_REGULAR); //  for doublons
         return new JsonResponse($modalites); 
    }

     /**
     * {@inheritdoc}
     */
    public function findSource($taxonomie,$famille,$espece,$trait, $modalite){
        $sources = [];
        $piecesEspece = explode("-", $espece);
        $espece = trim($piecesEspece[0]);

        $results = $this->basestorage->getSource($this->renameTaxonomie($taxonomie),$famille,$espece,$trait,$modalite);
         foreach($results as $row){
           if(!empty($row->source)){
             $sources[$row->source] = $row->source;
           }
         } 
         $sources = array_unique($sources, SORT_REGULAR); //  for doublons
         return new JsonResponse($sources); 
    }

  /**
   * {@inheritdoc}
   */
  public function buildDetailReference($id){
       // Recovered the result
        $result =  $this->basestorage->getReferenceById($id);
        $content = "";
        foreach($result as $item){
            $content .='<h4> N°'.$item->id.'</h4>';
            $content.='<span>Libelle reference : <i>'.$item->reference.'</i></span>';
        }
        $detail['info'] =  [
			    '#type' => 'markup',
			    '#markup' => $content,
        ];
       return $detail; 
  }

  /**
   * {@inheritdoc}
   *
   */
  public function buildDetailTrait($id){
       // Recovered the result
        $result =  $this->basestorage->getTraitById($id);
        $content = "";
        foreach($result as $item){
            $content .='<h4> N°'.$item->id.'</h4>';
            $content.='<span>Libelle trait : <i>'.$item->trait.'</i></span>';
        }
        $detail['info'] =  [
			    '#type' => 'markup',
			    '#markup' => $content,
        ];
       return $detail;
  }
  
  
   public function buildDetailModalite($id){
       // Recovered the result
        $result =  $this->basestorage->getModaliteById($id);
        $content = "";
        foreach($result as $item){
            $content .='<h4>'.$item->modalite.'</h4>';
            $content.='<span><i>'.$item->description.'</i></span>';
        }
        $detail['info'] =  [
			    '#type' => 'markup',
			    '#markup' => $content,
        ];
       return $detail;
  }
   

   /**
    * test 
    */
   public function test($taxonomie,$famille,$espece,$trait){

       $results = $this->basestorage->getModalite($taxonomie,$famille,$espece,$trait);
       $stocks = [];
       foreach ($results as $key) {
           $stocks[] = $key;
       }
      $reponse =[$taxonomie,$famille, $espece,$trait, $stocks];
      return $reponse;
   }
   
   
      /**
    * Change nom
    * Non Static
    */
    public  function renameTaxonomie($taxonomie){
        $taxonomie_string ="";
        switch($taxonomie){
            case 'plantae':
                $taxonomie_string = "Plante";
                break;
            case 'Plante':
                $taxonomie_string = "plantae";
                break;
            case 'Lepidoptera':
                $taxonomie_string="Papillon";
                break;
            case 'Papillon':
                $taxonomie_string ="Lepidoptera";
                break;
        }
        return $taxonomie_string;
   }
   
   /**
    * get Export
    */
   public function getExport($taxonomie,$famille,$espece,$trait,$modalite,$source){
        $donnees = $this->basestorage->FindExport($this->renameTaxonomie($taxonomie),$famille,$espece,$trait,$modalite,$source);
        return $donnees; 
   }

   /**
    * Change nom
    */
   public static  function genere_url($taxonomie, $cd_nom, $LB_NOM){
		$LB_NOM = str_replace(' ', '-', $LB_NOM);
		switch($taxonomie){
		    case 'plantae':
                $lien_exerne= "http://cbnbp.mnhn.fr/cbnbp/especeAction.do?action=pres&cdNom=".$cd_nom;
                break;
            case 'Lepidoptera':
                $lien_exerne="http://observatoire.cettia-idf.fr/taxon/rhopaloceres/atlas/especes/".$cd_nom."-".$LB_NOM;
                break;
		}
		return $lien_exerne;
   }
 
}
