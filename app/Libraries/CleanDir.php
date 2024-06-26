<?php

namespace App\Libraries;

/**
 * Nettoyage des répertoires
 *
 * @author christian
 */
class CleanDir
{
    /**
     * Supprime des fichiers d'un répertoire selon leur extension et leur âge
     * @param string $source_dir
     * @param string $ext
     * @param int $age
     * @return boolean
     */
    public static function cleanDir($source_dir, $ext = 'xlsx', $age = 3600)
    {
        if ($fp = @opendir($source_dir)) {
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            while (false !== ($fichier = readdir($fp))) {
                // On met le chemin du fichier dans une variable simple
                $chemin = $source_dir . $fichier;
                $infos = pathinfo($chemin);
                $extension = $infos['extension'];
                $age_fichier = time() - filemtime($chemin);

                if ($fichier != "." && $fichier != ".." && !is_dir($fichier) && ($extension == $ext) && ($age_fichier > $age)) {
                    unlink($chemin);
                }
            }

            closedir($fp);
        }

        return false;
    }
}
