<?php

namespace Drupal\natureparif_trait\Form;


/**
 * @file
 *
 * Contains Drupal\natureparif_trait\Form\BaseTraitsSearchForm
 */
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\natureparif_trait\BaseTraitsManager;
use Drupal\natureparif_trait\BaseTraitsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class BaseTraitsSearchForm extends FormBase{

    /**
     * Drupal\natureparif_trait\BaseTraitsManager definition.
     *
     * @var Drupal\natureparif_trait\BaseTraitsManager
     */
    protected $manager;

    /**
     * Constructor the object a BaseTraitsSearchForm
     */
    public function __construct(BaseTraitsManagerInterface $manager) {
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
     * {@inheritdoc}
     */
    public function getFormId(){
        // Unique ID of form
        return "base_traits_search_form";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state){

        //create a $form API array
        /* $form['searchTrait'] = [ 
            '#attributes' => array('id' => 'base-traits-search-form'),
        ]; */
        $form['searchTrait']['group_taxonomique'] =  [
              '#type' => 'select',
              '#title' => $this->t('Groupe taxonomique'),
              '#empty_option'=>t('- Sélectionner un groupe taxonomique -'),
              '#options' => $this->manager->findTaxonomies() ,
              '#description' => t('Ce champs  est obligatoire'),
              // '#required' =>TRUE,
         ];
        $form['searchTrait']['familles'] =  [
              '#type' => 'textfield',
              '#title' => $this->t('Familles'),
              '#default' => ($form_state->isValueEmpty('familles')) ? null : ($form_state->getValue('familles')),
              '#description' => t('Veuillez saisir au moins 2 lettres'),
              '#size' => 60,
        ];
        $form['searchTrait']['especes'] =  [
              '#type' => 'textfield',
              '#title' => $this->t('Espèces'),
              '#default' => ($form_state->isValueEmpty('especes')) ? null : ($form_state->getValue('especes')),
              '#description' => t('Veuillez saisir au moins 2 lettres'),
              '#size' => 60,
        ];
        $form['searchTrait']['traits'] = [
              '#type' => 'select',
              '#title' => $this->t('Traits'),
              '#empty_option'=>t('- Sélectionner un trait -'),
              '#validated' => TRUE,
               // Show this field only if the group_name or familes or especes  field is filled.
              '#states' => [
                 'visible' => [
                     array(
                         array(':input[name="group_taxonomique"]' => $this->manager->findValuesTaxonomies()),
                         'or', // xor
                         array('input[name="familles"]' => array('filled' => TRUE)),
                         'or', // xor
                         array('input[name="especes"]' => array('filled' => TRUE)),
                     ),
                 ],
            ],
            '#maxlength' => 128,
        ];
        $form['searchTrait']['modalites'] = [
              '#type' => 'select',
              '#title' => $this->t('Modalites'),
              '#empty_option'=>t('- Sélectionner une modalité -'),
              '#validated' => TRUE,
            
              // Show this field only if the group_name or familes or especes  field is filled.
            '#states' => [
                 'visible' => [
                     array(
                           array(),
                     ),
                 ],
            ], 
        ];
        $form['searchTrait']['sources'] = [
              '#type' => 'select',
              '#title' => $this->t('Sources'),
              '#empty_option'=>t('- Sélectionner une source -'),
              '#validated' => TRUE,
              
              // Show this field only if the group_name or familes or especes  field is filled.
              '#states' => [
                 'visible' => [
                     array(
                         array(),
                     ),
                 ],
            ], 
        ];
        $form['searchTrait']['field'] =  [
              '#type' => 'container',
              '#attributes' => ['id' => 'field'],
        ];
        $form['searchTrait']['submit'] = [
              '#type' => 'submit', 
              '#value' => t('Rechercher'),
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
         $especes_array = [];
         $espece = "rien";
         if(!empty($form_state->getValue('especes'))){
            $especes_array = explode("-",$form_state->getValue('especes'));
            $espece = trim($especes_array[0]);
         }
         $form_state->setRedirect(
             'natureparif_trait.pageTraitsContent',
             array(
                 'taxonomie'=> !empty($form_state->getValue('group_taxonomique')) ?  $form_state->getValue('group_taxonomique'): "rien",
                 'famille'=> !empty($form_state->getValue('familles')) ?  $form_state->getValue('familles') : "rien",
                 'espece'=> $espece,
                 'trait'=> !empty($form_state->getValue('traits')) ?  $form_state->getValue('traits') : "rien",
                 'modalite'=> !empty($form_state->getValue('modalites')) ?  $form_state->getValue('modalites'): "rien",
                 'source'=> !empty($form_state->getValue('sources')) ? $form_state->getValue('sources') :"rien",
             )
         );
    }
}