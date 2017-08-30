<?php

namespace Drupal\natureparif_trait;

/**
 * Interface BaseTraitsManagerInterface.
 *
 * @package Drupal\natureparif_trait
 */
interface BaseTraitsManagerInterface {

     /**
      *  Build page content major
      */
     public function buildContentMajor();

     /**
      * Build page content result trait
      * @param object, $request  
      */
     public function buildContentResult($request);

     /**
      * Build modal reference by id 
      * @param integer, $id
      */
     public function buildDetailReference($id);

     /**
      * Build modal trait by id 
      * @param integer, $id
      */
     public function buildDetailTrait($id);

     /**
      * Get all taxonomies
      * 
      * @return array taxonomique 
      */
     public function findTaxonomies();

    /**
     * Get Value taxonomies
     *
     * @return array value taxonomie
     */
    public function findValuesTaxonomies();

    /**
     * Get Famille
     * @param string| null, $famille
     * @param string| null, $taxonomie ="plantae"
     *
     * @return object famille 
     */
   public function findFamille($famille, $taxonomie);

   /**
     * Get espece
     * @param string| null, $espece
     * @param string| null, $taxonomie ="plantae"
     * @param string| null, $famille=null
     *
     * @return object Espece 
     */
   public function findEspece($espece, $taxonomie,$famille);

   /**
    * Get trait
    * @param string| null, $taxonomie ="plantae"
    * @param string| null, $famille=null
    * @param string| null, $espece
    */
  public function findTrait($taxonomie,$famille,$espece);
}
