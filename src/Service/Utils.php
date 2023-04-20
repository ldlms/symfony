<?php
namespace App\Service;
class Utils{
    /**
     * Passe la variable $input  et la nettoie. 
     * 
     * fonction htmlspecialchars : transforme les caractéres spéciaux  en string 
     * fonction strip_tags :  retourne que les strings 
     * fonction trim : Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
     * Suppression  de tous les caractéres de contrôle ASCII (de o à 31 inclusif).
     * 
     * @param  string $input
     * @return null|string
     */
    public static function cleanInput(string $input):?string{
        return htmlspecialchars(strip_tags(trim($input,"\x00..\x1F")));
        }
    
    /**
     * @param string $value
     * @return null|string
     */
    public static function cleanInputStatic(string $value):?string{
        return htmlspecialchars(strip_tags(trim($value)));
    }

    /**
     * @param string $value
     * @return null|string
     */
    // public function cleanInput(string $value):?string{
    //     return htmlspecialchars(strip_tags(trim($value)));
    // }

    /**
     * @param string  $date
     * @param string  $format
     * @return bool
    */
    public static function isValid($date, $format = 'Y-m-d'):bool{
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }


    /**
     * @param string  $file
     * @return null|string
    */
    static function get_file_extension($file):?string {
        return substr(strrchr($file,'.'),1);
    }
}
?>
