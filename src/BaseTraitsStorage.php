<?php

/**
 * @file 
 *
 * I use three database, whose are : Drupal, trait, referenciel.
 * instance in constructor 
 * I have implement  one interface ('BaseTraitsStorageInterface').
 *
 * @package  \Drupal\natureparif_trait\BaseTraitsStorage
 */

 namespace Drupal\natureparif_trait;

 use Drupal\Core\Database\Database;
 use Drupal\Core\Database\Connection;
 use Drupal\natureparif_trait\BaseTraitsStorageInterface;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\DependencyInjection\Container;
 use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseTraitsStorage 
 */
class BaseTraitsStorage implements  BaseTraitsStorageInterface{

     /**
      * Construct a BaseTraitStorage
      *  @param \Drupal\Core\Database $database
      *  The current database connection.
      */
     private $database;

     /**
      * Constructor
      * @param  \Drupal\Core\Database\Connection;
      */
     public function __construct(Connection $connection) {
        $this->database = $connection; 
     }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
       return new static(
         $container->get('database')
      );
    }
   
    /**
     * Return taxonomie  
     * 
     * {@inheritdoc}
     */
    public function getAllTaxonomies(){
        $req  =  $this->database->select('traits.trt_especes_modalites', 'tem');
        $req->distinct();
        $req->innerJoin('REFERENTIELS.REF_taxref','rt', 'tem.cd_nom = rt.CD_NOM');
        $req->fields('rt',array('grp'));
        $reponse = $req->execute();
        return $reponse;
    }

    /**
     * Return famille
     *
     * {@ineritdoc}
     */ 
    public function getTraitByFamille($famille){
        $req  =  $this->database->select('REFERENTIELS.REF_taxref_classif', 'rtc')
          ->fields('rtc',array('Famille'))
          ->condition('Famille',$famille."%",'LIKE')
          ->execute();
        return $req; 
    }

    /**
     *
     * {@inheritdoc}
     */
    public function getTraitByEspece($espece){
        $req  =  $this->database->select('traits.trt_especes', 'te')
          ->fields('te',array('Especes'))
          ->condition('Especes',$espece."%",'LIKE')
          ->execute();
        return $req;
    }

    /**
     * {@inheritdoc}
     */
    public function getfamille($famille, $taxonomie)
    {
         $req = $this->database->select('REFERENTIELS.REF_taxref','rt');
         $req->distinct();
         $req->innerJoin('REFERENTIELS.REF_taxref_classif','rtc', 'rt.id_classif = rtc.id_classif');
         $req->fields('rtc',array('FAMILLE'));
         $req->innerJoin('traits.trt_especes_modalites', 'tem', 'tem.cd_nom = rt.CD_NOM');
        
         if(!empty($taxonomie) && $taxonomie !="rien")  $req->condition('rt.grp',$taxonomie,'=');
         if(!empty($famille) && $famille !="rien")  $req->condition('rtc.FAMILLE', $famille."%",'LIKE');
         $reponse =  $req->execute()->fetchAll(); 
         return  $reponse;
    }

    /**
     * {@inheritdoc}
     */
    public function getEspece($espece, $taxonomie, $famille)
    {
        $req  = $this->database->select('REFERENTIELS.REF_taxref','rt');
        $req->fields('rt',array('LB_NOM'));
        $req->innerJoin('REFERENTIELS.REF_taxref_classif','rtc', 'rt.id_classif = rtc.id_classif');
        $req->innerJoin('traits.trt_especes_modalites', 'tem', 'tem.cd_nom = rt.CD_NOM ');
        $req->addExpression('GROUP_CONCAT(DISTINCT rt.NOM_VERN)');
        
        if(!empty($taxonomie) && $taxonomie !="nothing")  $req->condition('rt.grp',$taxonomie,'=');
        if(!empty($famille) && $famille !="nothing") $req->condition('rtc.FAMILLE',$famille,'=');
        if(!empty($espece) && $espece !="nothing") $req->condition('rt.LB_NOM', $espece."%",'LIKE');

        $req->groupBy('rt.LB_NOM');
        $reponse =  $req->execute()->fetchAll();
        return  $reponse;
    }
   
    /**
     * {@inheritdoc}
     */
    public function getReferenceById($id){
        $req = $this->database->select('traits.trt_references','tr');
        $req->distinct();
        $req->addField('tr','id');
        $req->addField('tr','libelle','reference');
        $req->condition('tr.id',$id,'=');
        $reponse = $req->execute();
        return  $reponse;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getTraitById($id){
        $req = $this->database->select('traits.trt_traits','tt');
        $req->distinct();
        $req->addField('tt','id');
        $req->addField('tt','libelle','trait');
        $req->condition('tt.id',$id,'=');
        $reponse = $req->execute();
        return  $reponse;
    }
	
	public function getModaliteById($id){
        $req = $this->database->select('traits.trt_modalites','tm');
        $req->distinct();
        $req->addField('tm','id');
        $req->addField('tm','libelle','modalite');
        $req->addField('tm','description');
        $req->condition('tm.id',$id,'=');
        $reponse = $req->execute();
        return  $reponse;
    }

   /**
    * {@inheritdoc}
    */
   public function getTrait($taxonomie="rien",$famille="rien",$espece="rien"){
        $reponse = null;
        $req = $this->database->select('traits.trt_especes_modalites','tem');
        $req->distinct();
        $req->addField('tt','libelle','trait');
        $req->innerJoin('REFERENTIELS.REF_taxref','rt','tem.cd_nom = rt.CD_NOM');
        $req->innerJoin('traits.trt_modalites','tm','tem.id_modalite = tm.id');
        $req->innerJoin('traits.trt_traits','tt','tm.Id_trait = tt.id');
        $req->innerJoin('REFERENTIELS.REF_taxref_classif','rtc','rtc.id_classif = rt.id_classif');

        if(!empty($taxonomie) && $taxonomie !="rien") $req->condition('rt.grp',$taxonomie,'=');
        if(!empty($famille) && $famille !="rien" && $famille !="Non results") $req->condition('rtc.FAMILLE',$famille,'=');
        if(!empty($espece) && $espece !="rien" && $espece !="Non results") $req->condition('rt.LB_NOM', $espece,"=");
        $reponse =  $req->execute()->fetchAll(); 
        return $reponse;
   }
   
   /**
    * {@inheritdoc}
    */
   public function getModalite($taxonomie="rien",$famille="rien",$espece="rien",$trait="rien"){
       $reponse = null;
       $req = $this->database->select('traits.trt_especes_modalites','tem');
       $req->distinct();
       $req->addField('tm','libelle','modalite');
       $req->innerJoin('REFERENTIELS.REF_taxref','rt','tem.cd_nom = rt.CD_NOM');
       $req->innerJoin('traits.trt_modalites', 'tm', 'tem.id_modalite = tm.id');
       $req->innerJoin('traits.trt_traits', 'tt', 'tm.Id_trait = tt.id');
       $req->innerJoin('REFERENTIELS.REF_taxref_classif', 'rtc', 'rt.id_classif = rtc.id_classif'); 
       if(!empty($taxonomie) && $taxonomie !="rien") $req->condition('rt.grp',$taxonomie,'='); 
       if(!empty($famille) && $famille !="rien" && $famille !="Non results")$req->condition('rtc.FAMILLE',$famille,'=');
       if(!empty($espece) && $espece !="rien" && $espece !="Non results") $req->condition('rt.LB_NOM', $espece,"=");
       if(!empty($trait) && $trait !="rien") $req->condition('tt.libelle', $trait,"=");
           
       $reponse =  $req->execute()->fetchAll(); 
       return $reponse;
   }

   /**
    * {@inheritdoc}
    */
   public function getSource($taxonomie="rien",$famille="rien",$espece="rien",$trait="rien", $modalite="rien"){
       $reponse = null;
       $req = $this->database->select('traits.trt_especes_modalites','tem');
       $req->distinct();
       $req->addField('ts','libelle','source');
       $req->innerJoin('REFERENTIELS.REF_taxref','rt','tem.cd_nom = rt.CD_NOM');
       $req->innerJoin('traits.trt_modalites','tm','tem.id_modalite = tm.id');
       $req->innerJoin('traits.trt_traits','tt','tm.Id_trait = tt.id');
       $req->innerJoin('traits.trt_sources','ts','ts.id = tem.id_source');
       $req->innerJoin('REFERENTIELS.REF_taxref_classif','rtc','rt.id_classif = rtc.id_classif');
      
       if(!empty($taxonomie) && $taxonomie !="rien") $req->condition('rt.grp',$taxonomie,'=');
       if(!empty($famille) && $famille !="rien" && $famille!="Non results") $req->condition('rtc.FAMILLE',$famille,'=');
       if(!empty($espece) && $espece !="rien" && $espece!="Non results") $req->condition('rt.LB_NOM', $espece,"=");
       if(!empty($trait) && $trait !="rien")  $req->condition('tt.libelle', $trait,"=");
       if(!empty($modalite) && $modalite != "rien" ) $req->condition('tm.libelle', $modalite,"=");
       $reponse =  $req->execute()->fetchAll(); 
       return $reponse;
   }

   /**
    *{@inheritdoc}
    */ 
    public function FindAll($taxonomie,$famille,$espece,$trait,$modalite,$source,$header =[],$limit=10){
        $req = $this->database->select('traits.trt_especes_modalites','tem');
        $req->distinct();
        $req->addField('rt','grp');
        $req->addField('rtc','FAMILLE');
        $req->addField('tem','cd_nom');
        $req->addField('rt','LB_NOM');
        $req->addField('rt','NOM_VERN');
        $req->addField('tt','libelle','trait');
        $req->addField('tm','libelle','modalite');
        $req->addField('ts','libelle', 'source');
        $req->addField('tr','libelle','reference');
        $req->addField('user', 'name');
        $req->addField('tr','id','reference_id');
        $req->addField('tt','id','trait_id');  
		$req->addField('tm','id','modalite_id'); 		
        $req->innerJoin('REFERENTIELS.REF_taxref','rt','tem.cd_nom = rt.CD_NOM');
        $req->innerJoin('traits.trt_modalites', 'tm', 'tem.id_modalite = tm.id');
        $req->innerJoin('traits.trt_traits', 'tt', 'tm.Id_trait = tt.id');
        $req->innerJoin('traits.trt_sources', 'ts', 'ts.id = tem.id_source');
        $req->innerJoin('traits.trt_references', 'tr', 'tr.id = tem.id_reference');
        $req->innerJoin('`8drupal`.users_field_data ', 'user', 'user.uid = tem.Id_saisie');
        $req->innerJoin('REFERENTIELS.REF_taxref_classif', 'rtc', 'rt.id_classif = rtc.id_classif');
         
        if(!empty($taxonomie) && $taxonomie !="rien") $req->condition('rt.grp',$taxonomie,'=');
        if(!empty($famille) && $famille !="rien") $req->condition('rtc.FAMILLE',$famille,'=');
        if(!empty($espece) && $espece !="rien") $req->condition('rt.LB_NOM', $espece,"=");
        if(!empty($trait) && $trait !="rien")  $req->condition('tt.libelle', $trait,"=");
        if(!empty($modalite) && $modalite != "rien" ) $req->condition('tm.libelle', $modalite,"=");
        if(!empty($source) && $source != "rien" ) $req->condition('ts.libelle', $source,"=");

         // The actual action of sorting the rows is here.
         $tab_sort = $req->extend('\Drupal\Core\Database\Query\TableSortExtender')
                        ->orderByHeader($header);
         // Limit the rows to 10 for each page.
         $pager = $tab_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
                         ->limit($limit);
         $reponse =$pager ->execute();
        
         return $reponse;
    }

    /**
    * @test
    */ 
    public function FindExport($taxonomie,$famille,$espece,$trait,$modalite,$source){
        $req = $this->database->select('traits.trt_especes_modalites','tem');
        $req->distinct();
        $req->addField('rt','grp');
        $req->addField('rtc','FAMILLE');
        $req->addField('tem','cd_nom');
        $req->addField('rt','LB_NOM');
        $req->addField('rt','NOM_VERN');
        $req->addField('tt','libelle','trait');
        $req->addField('tm','libelle','modalite');
        $req->addField('ts','libelle', 'source');
        $req->addField('tr','libelle','reference');
        $req->addField('user', 'name');
        $req->addField('tr','id','reference_id');
        $req->innerJoin('REFERENTIELS.REF_taxref','rt','tem.cd_nom = rt.CD_NOM');
        $req->innerJoin('traits.trt_modalites', 'tm', 'tem.id_modalite = tm.id');
        $req->innerJoin('traits.trt_traits', 'tt', 'tm.Id_trait = tt.id');
        $req->innerJoin('traits.trt_sources', 'ts', 'ts.id = tem.id_source');
        $req->innerJoin('traits.trt_references', 'tr', 'tr.id = tem.id_reference');
        $req->innerJoin('`8drupal`.users_field_data ', 'user', 'user.uid = tem.Id_saisie');
        $req->innerJoin('REFERENTIELS.REF_taxref_classif', 'rtc', 'rt.id_classif = rtc.id_classif');
        
         
        if(!empty($taxonomie) && $taxonomie !="rien") $req->condition('rt.grp',$taxonomie,'=');
        if(!empty($famille) && $famille !="rien") $req->condition('rtc.FAMILLE',$famille,'=');
        if(!empty($espece) && $espece !="rien") $req->condition('rt.LB_NOM', $espece,"=");
        if(!empty($trait) && $trait !="rien")  $req->condition('tt.libelle', $trait,"=");
        if(!empty($modalite) && $modalite != "rien" ) $req->condition('tm.libelle', $modalite,"=");
        if(!empty($source) && $source != "rien" ) $req->condition('ts.libelle', $source,"=");

        $reponse = $req->execute();
        return $reponse;
    }
}