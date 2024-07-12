<?php

namespace app\controllers;

use app\models\ApiPost;
use Yii;
use yii\web\Controller;

class SiteController extends Controller{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
	
	/**
	 * Displays test 1.
	 *
	 * @return string
	 */
	public function actionPosts(): string
    {
        $url = 'https://jsonplaceholder.typicode.com/posts';
        $result = [];

        try {
            $response = file_get_contents($url);

            if ($response === false) {
                throw new \Exception('Error al realizar la solicitud a la API.');
            }

            $postsArray = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error al decodificar la respuesta JSON: ' . json_last_error_msg());
            }

            foreach ($postsArray as $post) {
                $result[] = new ApiPost(
                    $post['id'] ?? null,
                    $post['userId'] ?? null,
                    $post['title'] ?? null,
                    $post['body'] ?? null,
                );
            }

        } catch (\Exception $e) {
            Yii::error("Error en la acción 'posts': " . $e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Hubo un problema al cargar los posts.');
            return $this->render('posts', [
                'apiPosts' => [],
            ]);
        }

        return $this->render('posts', [
            'apiPosts' => $result,
        ]);
	}
}
