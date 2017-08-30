<?php

/**
 * @file
 *
 * @package \Drupal\natureparif_trait\Controller\PhpExcelController
 */
namespace Drupal\natureparif_trait\Controller;

use Drupal\natureparif_trait\BaseTraitsStorage;
use Drupal\natureparif_trait\BaseTraitsManagerInterface; 
use Drupal\natureparif_trait\BaseTraitsManager;

class PhpExcelController 
{
    /**
     *
     */
    public static function  data($taxonomie ="plantae",$famille ="rien",$espece ="rien",$trait ="rien",$modalite ="rien", $source ="rien"){

	    $manager = \Drupal::service('base_trait.manager');
		$donnees= $manager->getExport($taxonomie,$famille,$espece,$trait,$modalite,$source);
		return $donnees;
        
    }
  

    /**
     * return tableau excel  
     */   
    public static function ExportExcel($taxonomie,$famille,$espece,$trait,$modalite,$source){
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=natureparif_base_traits.xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");

        flush(); // Vide les tampons de sortie du système
        
        // include the files 
        require_once 'sites/libraries/phpexcel/Classes/PHPExcel.php';
        require_once 'sites/libraries/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';

     

        /* $taxonomie  = "plantae"; 
        $famille= "rien"; // Asteraceae
        $espece ="Achillea millefolium"; 
        $trait ="couleur_fleur";
        $modalite ="rien"; // Blanc
        $source= "rien"; */

        $sheet = new \PHPExcel();
        // retrieve all data
        $lesDonnees =  PhpExcelController::data($taxonomie,$famille,$espece,$trait,$modalite,$source);
        $manager = \Drupal::service('base_trait.manager');
        

        //Set properties
        $sheet->getProperties()
            ->setCreator('christian shungu')
            ->setLastModifiedBy('base de trait')
            ->setTitle("Base de traits")
            ->setLastModifiedBy('Base de traits')
            ->setDescription('This data comes from in 3 data : traits, refereniel, drupal')
            ->setSubject('Base de trait natureparif')
            ->setKeywords('excel php office phpexcel lakers')
            ->setCategory('programming');

        //Add some data
         $sheet->setActiveSheetIndex(0);
         $activeSheet = $sheet->getActiveSheet();

        //Rename sheet
         $activeSheet->setTitle('Base de trait');

         /*
         * TITLE
         */
        //Set style Title
        $styleArrayTitle = array(
             'font' => array(
                'bold' => true,
                'color' => array('rgb' => '161617'),
                'size' => 18,
                'name' => 'Bodoni MT'
         ));

         $activeSheet->getCell('A1')->setValue('Base de données traits');
         $activeSheet->getStyle('A1')->applyFromArray($styleArrayTitle);

        /*
         * HEADER
         */
        //Set Background        
        $activeSheet->getStyle('A3:J3')
            ->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('43454A');

        //Set style Head
        $styleArrayHead = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => 'ffffff'),
                'size' => 12,
                'name' => 'Times New Roman'
        ));

         //Set values
         $activeSheet->getCell('A3')->setValue('Groupe Taxonomique');
         $activeSheet->getCell('B3')->setValue('Famille');
         $activeSheet->getCell('C3')->setValue('Cd_nom');
         $activeSheet->getCell('D3')->setValue('Nom scientifique');
         $activeSheet->getCell('E3')->setValue('Nom vernaculaire');
         $activeSheet->getCell('F3')->setValue('Traits');
         $activeSheet->getCell('G3')->setValue('Modalité');
         $activeSheet->getCell('H3')->setValue('Source');
         $activeSheet->getCell('I3')->setValue('Référence');
         $activeSheet->getCell('J3')->setValue('Utilisateur');

         $activeSheet->getStyle('A3:J3')->applyFromArray($styleArrayHead);
         
         $i = 4;
        foreach($lesDonnees as $uneDonnees){ 
             $activeSheet->setCellValue('A' . $i, $manager->renameTaxonomie($uneDonnees->grp));
             $activeSheet->setCellValue('B' . $i, $uneDonnees->FAMILLE);
             $activeSheet->setCellValue('C' . $i, $uneDonnees->cd_nom);
             $activeSheet->setCellValue('D' . $i, $uneDonnees->LB_NOM);
             $activeSheet->setCellValue('E' . $i, $uneDonnees->NOM_VERN);
             $activeSheet->setCellValue('F' . $i, $uneDonnees->trait);
             $activeSheet->setCellValue('G' . $i, $uneDonnees->modalite);
             $activeSheet->setCellValue('H' . $i, $uneDonnees->source);
             $activeSheet->setCellValue('I' . $i, $uneDonnees->reference);
             $activeSheet->setCellValue('J' . $i, $uneDonnees->name);
             $i++;
        } 

        $writer = new \PHPExcel_Writer_Excel2007($sheet);
        ob_end_clean(); // — Détruit les données du tampon de sortie et éteint la temporisation de sortie
        $writer->save('php://output');
        exit();


	}
}