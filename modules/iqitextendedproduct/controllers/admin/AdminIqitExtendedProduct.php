<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminIqitExtendedProductController extends ModuleAdminController
{
    const ACCESS_RIGHTS = 0775;
    const SOURCE_INDEX = _PS_MODULE_DIR_ . 'iqitextendedproduct/index.php';
    const UPLOAD_DIR = _PS_MODULE_DIR_ . 'iqitextendedproduct/uploads/';

   
    public function ajaxProcessUploaderThreeSixty()
    {

        $idProduct = (int) Tools::getValue('id_product');
        $folder = 'threesixty/';

        $product = new Product((int) $idProduct);
        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }
        header('Content-Type: application/json');
        $step = (int) Tools::getValue('step');

        if ($step == 1) {
            $image_uploader = new HelperImageUploader('threesixty-file-upload');
            $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg'));
            $files = $image_uploader->process();
            $new_destination = $this->getPathForCreation($idProduct, $folder);

            foreach ($files as &$file) {
                $filename = uniqid() . '.jpg';
                $error = 0;
                if (!ImageManager::resize($file['save_path'], $new_destination . $filename, null, null, 'jpg', false, $error)) {
                    switch ($error) {
                        case ImageManager::ERROR_FILE_NOT_EXIST:
                        $file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                        break;
                        case ImageManager::ERROR_FILE_WIDTH:
                        $file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                        break;
                        case ImageManager::ERROR_MEMORY_LIMIT:
                        $file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                        break;
                        default:
                        $file['error'] = Tools::displayError('An error occurred while copying image.');
                        break;
                    }
                    continue;
                }
                unlink($file['save_path']);
                unset($file['save_path']);
                $file['status'] = 'ok';
                $file['name'] = $filename;
            }
            die(json_encode($files[0]));
        } elseif ($step == 2) {
            $file = (string) Tools::getValue('file');
            if (file_exists(self::UPLOAD_DIR . $folder . $idProduct . '/' . $file)) {
                $res = @unlink(self::UPLOAD_DIR . $folder . $idProduct . '/' . $file);
            }
            if ($res) {
                die('ok');
            } else {
                die('error');
            }
        }
    }

        private function getPathForCreation($id_product, $folder)
    {
        $path = $this->getFolder($id_product);
        $this->createFolder($id_product, self::UPLOAD_DIR . $folder);
        return self::UPLOAD_DIR . $folder . $path;
    }

    private function createFolder($id_product, $folder)
    {
        if (!file_exists($folder . $this->getFolder($id_product))) {
            $success = @mkdir($folder . $this->getFolder($id_product), self::ACCESS_RIGHTS, true);
            $chmod = @chmod($folder . $this->getFolder($id_product), self::ACCESS_RIGHTS);
            if (($success || $chmod)
                && !file_exists($folder . $this->getFolder($id_product) . 'index.php')
                && file_exists(self::SOURCE_INDEX)) {
                return @copy(self::SOURCE_INDEX, $folder . $this->getFolder($id_product) . 'index.php');
            }
        }
        return true;
    }

    private function getFolder($id_product)
    {
        if (!is_numeric($id_product)) {
            return false;
        }
        $folders = str_split((string) $id_product);
        return implode('/', $folders) . '/';
    }


}
