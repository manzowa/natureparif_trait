<?php

/**
 * @file 
 * 
 * @package \Drupal\natureparif_trait\BaseTraitsStorageInterface
 */

 namespace Drupal\natureparif_trait;

 /**
  * Interface BaseTraitsStorageInterface 
  */
interface BaseTraitsStorageInterface{

    /**
     * Return all taxonomies 
     *
     * @return All groupe taxanomies 
     */
    public function getAllTaxonomies();

    /**
     * Return all fammille
     *
     * @param  string $famille
     *
     * @return array  famille
     */
    public function getTraitByFamille($famille);

    /**
     * Return all  Eespece
     *
     * @param  string  $espece
     *
     * @return array  espece
     */
    public function getTraitByEspece($espece);


     /**
      * Return all famille of the groupe taxonomie choose. 
      *
      * @param string | null, Contains $taxonomie
      * @param string | null, Contains $famille
      *
      * @return object famille  
      */
     public function getFamille($famille, $taxonomie);

     /**
      * Return all famille of the groupe taxonomie choose. 
      *
      * @param string | null, Contains $taxonomie
      * @param string | null, Contains $espece
      * @param string | null, Contains $famille
      *
      * @return object Espece  
      */
     public function getEspece($espece, $taxonomie,$famille);

     /**
      * Return All info by groupe taxonomie, famille, Especes, traits, Modalite, source  
      *
      * @param String, should contains $taxonomie  
      * @param String, Should contains $famille 
      * @param String, should contains $espece 
      * @param String, should contains $trait 
      * @param String, should contains $modalite
      * @param String, should contains $source
      * @param array , should contains $header
      *
      * @return  object.  
      */
     public function FindAll($taxonomie,$famille,$espece,$trait,$modalite,$source,$header =[],$limit=10);

     /**
      * Return info reference 
      *
      * @param integer , should contains $id
      *
      * @return object. 
      */
     public function getReferenceById($id);

     /**
      * Return info trait 
      *
      * @param integer , should contains $id
      *
      * @return object. 
      */
     public function getTraitById($id);


     /**
      * Return trait
      * @param String, should contains $taxonomie  
      * @param String, Should contains $famille 
      * @param String, should contains $espece 
      * @return  object.  
      *
      */
    public function getTrait($taxonomie="rien",$famille="rien",$espece="rien");

    /**
     * Return modalite
     * @param String, should contains $taxonomie  
     * @param String, Should contains $famille 
     * @param String, should contains $espece 
     * @param String, should contains $trait 
     */
    public function getModalite($taxonomie="rien",$famille="rien",$espece="rien",$trait="rien");

    /**
     * Return Sources
     * @param String, should contains $taxonomie  
     * @param String, Should contains $famille 
     * @param String, should contains $espece 
     * @param String, should contains $trait 
     * @param String, should contains $modalite
     */
    public function getSource($taxonomie="rien",$famille="rien",$espece="rien",$trait="rien",$modalite="rien");

}
