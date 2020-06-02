<?php
/**
 * Created by javier
 * Date: 25/03/17
 * Time: 11:10
 */

namespace CakepressEditor\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * Class UploadController
 * @package CakepressEditor\Controller
 */
class UploadController extends AppController
{
    /**
     * @return \Cake\Network\Response|null
     */
    public function handleImagesUpload()
    {
        $this->request->allowMethod('post');

        try {
            $validator = new Validator();
            $validator->add('images', 'fileType', [
                'rule' => ['extension', ['gif', 'jpeg', 'png', 'jpg']],
                'message' => __('Tipo de archivo incorrecto')
            ]);

            if ($errors = $validator->errors($this->request->data)) {
                throw new \RuntimeException(__('Error al subir el archivo: {0}', [json_encode(Hash::flatten($errors))]));
            }

            $destinyFolder = new Folder(sprintf("%suploads/cakepress-editor/%s/", WWW_ROOT, date('Y-m-d')), true);
            $file = new File($this->request->data['images']['tmp_name']);

            $dest = $destinyFolder->path . uniqid(date('Ymd')) . '.' . pathinfo($this->request->data('images.name'), PATHINFO_EXTENSION);

            if (!$file->copy($dest)) {
                throw new \RuntimeException(__('Error al mover el archivo de {0} a {1}', [$file->path, $dest]));
            }

            $dest = new File($dest);

            $this->response->body(json_encode([
                'name' => $dest->name(),
                'url' => Router::url(str_replace(WWW_ROOT, '/', $dest->path), true)
            ]));
        } catch (\Exception $e) {
            $this->response->body(json_encode(['error' => $e->getMessage()]));
        }

        $this->response->type('json');

        return $this->response;
    }
}