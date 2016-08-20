<?php

/**
 * Modulo del controlador de la pagina de comentarios.
 */

namespace SoftnCMS\controllers\admin;

use SoftnCMS\controllers\BaseController;
use SoftnCMS\controllers\Messages;
use SoftnCMS\models\admin\Comments;
use SoftnCMS\models\admin\Comment;
use SoftnCMS\models\admin\CommentUpdate;
use SoftnCMS\models\admin\CommentInsert;
use SoftnCMS\models\admin\CommentDelete;

/**
 * Clase del controlador de la pagina de comentarios.
 *
 * @author Nicolás Marulanda P.
 */
class CommentController extends BaseController {

    /**
     * Metodo llamado por la función INDEX.
     * @return array
     */
    protected function dataIndex() {
        $comments = Comments::selectAll();
        $output = $comments->getComments();

        return ['comments' => $output];
    }

    /**
     * Metodo llamado por la función INSERT.
     * @return array
     */
    protected function dataInsert() {
        global $urlSite;

        if (filter_input(\INPUT_POST, 'publish')) {
            $dataInput = $this->getDataInput();
            $insert = new CommentInsert($dataInput['commentAutor'], $dataInput['commentAuthorEmail'], $dataInput['commentStatus'], $dataInput['commentContents'], $dataInput['postID'], $_SESSION['usernameID']);

            if ($insert->insert()) {
                Messages::addSuccess('Comentario publicado correctamente.');
                //Si todo es correcto se muestra el comentario en la pagina de edición.
                header("Location: $urlSite" . 'admin/comment/update/' . $insert->getLastInsertId());
                exit();
            }
            Messages::addError('Error al publicar el comentario');
        }

        return [
            //Datos por defecto a mostrar en el formulario.
            'comment' => Comment::defaultInstance(),
            /*
             * Booleano que indica si muestra el encabezado
             * "Publicar nuevo comentario" si es FALSE 
             * o "Actualizar comentario" si es TRUE
             */
            'actionUpdate' => \FALSE
        ];
    }

    /**
     * Metodo llamado por la función UPDATE.
     * @param int $id
     * @return array
     */
    protected function dataUpdate($id) {
        global $urlSite;

        $comment = Comment::selectByID($id);

        //En caso de que no exista.
        if (empty($comment)) {
            Messages::addError('Error. El commentario no existe.');
            header("Location: $urlSite" . 'admin/comment');
            exit();
        }

        if (filter_input(\INPUT_POST, 'update')) {
            $dataInput = $this->getDataInput();
            $update = new CommentUpdate($comment, $dataInput['commentAutor'], $dataInput['commentAuthorEmail'], $dataInput['commentStatus'], $dataInput['commentContents']);

            //Si ocurre un error la función "$update->update()" retorna FALSE.
            if ($update->update()) {
                Messages::addSuccess('Comentario actualizado correctamente.');
                $comment = $update->getComment();
            } else {
                Messages::addError('Error al actualizar el comentario.');
            }
        }

        return [
            //Instancia Comment
            'comment' => $comment,
            /*
             * Booleano que indica si muestra el encabezado
             * "Publicar nuevo comentario" si es FALSE 
             * o "Actualizar comentario" si es TRUE
             */
            'actionUpdate' => \TRUE
        ];
    }

    /**
     * Metodo llamado por la función DELETE.
     * @param int $id
     * @return array
     */
    protected function dataDelete($id) {
        /*
         * Ya que este metodo no tiene modulo vista propio
         * se carga el modulo vista INDEX, asi que se retornan los datos
         * para esta vista.
         */

        $delete = new CommentDelete($id);

        if ($delete->delete()) {
            Messages::addSuccess('Comentario borrado correctamente.');
        } else {
            Messages::addError('Error al borrar el comentario.');
        }

        return $this->dataIndex();
    }

    /**
     * Metodo que obtiene los datos de los campos INPUT del formulario.
     * @return array
     */
    protected function getDataInput() {
        return [
            'commentAutor' => \filter_input(\INPUT_POST, 'commentAutor'),
            'commentAuthorEmail' => \filter_input(\INPUT_POST, 'commentAuthorEmail'),
            'postID' => \filter_input(\INPUT_POST, 'postID'),
            'commentStatus' => \filter_input(\INPUT_POST, 'commentStatus'),
            'commentContents' => \filter_input(\INPUT_POST, 'commentContents'),
        ];
    }

}