<?php

namespace Drupal\proedm\Controller;

use Drupal;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\comment\Entity\Comment;

use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Component\Utility\Variable;

use Drupal\Component\Datetime\DateTimePlus;

use DateTime;
use DateInterval;
use DateTimeZone;
use PDO;

/**
 * Implementing our example JSON api.
 */
class AppController {
	
	private const API_KEY = 'm2j7GGSKubPzr3WdcV7n';

	/**
	 * Callback for the API.
	 */
	public function access(){
		$request = !is_null(Drupal::request()->request->get('api_key'))? Drupal::request()->request->all():Json::decode(Drupal::request()->getContent());
			
		$api_key = isset($request['api_key'])? $request['api_key'] : NULL;
		
		return ($api_key == self::API_KEY)? AccessResult::allowed() : AccessResult::forbidden();
	}
	
	public function content(Request $request) {
		$content = [];
		
		//Content
		/*$storage = Drupal::entityTypeManager()->getStorage('node');
		$aviso = $storage->load(4);
		$terminos = $storage->load(5);
		$content['content'] = [['id' => 'aviso', 'body' => $aviso->body->value], ['id' => 'terminos', 'body' => $terminos->body->value]];
		*/
			
		//Users
		$users = [];
		$storage = Drupal::entityTypeManager()->getStorage('user');
		$items = $storage->loadMultiple();
		foreach($items as $id => $account){
			if(!$account->isActive() || !($account->hasRole('parlamentario') || $account->hasRole('public'))) continue;			
			$users[] = static::accountToArray($account);
		}
		$content['users'] = $users;
		
		//Posts
		$posts = [];
		$items = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'blog_post', 'status' => 1]);
		foreach($items as $id => $item){
			$comments = $item->field_blog_comments->first()->getValue();			
			$posts[] = ['id' => $id, 'fecha' => (int) $item->created->value, 'uid' => (int) $item->getOwnerId(), 'title' => $item->title->value, 'body' => $item->body->value, 'comments' => $comments['comment_count']];
			if(count($posts) > 100) break;
		}
		$content['posts'] = $posts;
			
		$response = new JsonResponse($content);//CacheableJsonResponse($content);
			
		return $response;
	}
	
	public function help(Request $request) {		
		$request = Json::decode($request->getContent());
		
		$webform_id = isset($request['webform_id'])? $request['webform_id']:null;
		if(!$webform_id) return new JsonResponse(['error_id' => 1, 'error_mensaje' => 'webform_id no está definido.']);
		
		//$request['uid'] = 22;
		$request['data'] = $request;		
		$submission = WebformSubmission::create($request);
		$submission->save();
		return new JsonResponse(['submission_id' => $submission->id()]);		
	}
	
	public static function accountToArray($account){
		$picture = $account->user_picture->isEmpty()? null : file_create_url($account->user_picture->entity->getFileUri());			
		$fullname = (strlen(trim($account->field_fullname->value)) > 3)? $account->field_fullname->value:$account->getDisplayName();
		$vip = $account->hasRole('parlamentario')? 1:0;
			
		$user = ['uid' => (int) $account->id(), 'name' => $fullname, 'email' => $account->getEmail(), 'picture' => $picture, 'tel' => $account->field_tel->value, 'institution' => $account->field_institution->value, 'facebook' => $account->field_facebook->value, 'twitter' => $account->field_twitter->value, 'vip' => $vip, 'country' => $account->field_country->value];
		
		return $user;
	}
	
	public static function accountToJsonResponse($account){		
		$user = static::accountToArray($account);			
		return new JsonResponse($user);
	}

	public function sign_in(Request $request) {
		$request = Json::decode($request->getContent());
		
		$email = $request['email'];		
		$account = user_load_by_mail($email);
		
		if(!$account) return new JsonResponse(['error_id' => 2, 'error_mensaje' => new TranslatableMarkup('No se encontró un usuario registrado con el correo @email', ['@email' => $email])]);
				
		$uid = Drupal::service('user.auth')->authenticate($account->name->value, $request['password']);
		if(!$uid) return new JsonResponse(['error_id' => 3, 'error_mensaje' => 'Contraseña inválida, revise su contraseña.']);
		
		return static::accountToJsonResponse($account);
	}
		
	public function usuario_edit(Request $request) {
		$request = Json::decode($request->getContent());
				
		$account = User::load($request['uid']);
		if(!$account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del usuario no es válido.')]);
		
		$account->set('field_fullname', $request['name']);
		$account->set('field_tel', isset($request['tel'])? $request['tel']:'');
		$account->set('field_country', isset($request['country'])? $request['country']:'');
		$account->set('field_institution', isset($request['institution'])? $request['institution']:'');
		$account->set('field_facebook', isset($request['facebook'])? $request['facebook']:'');
		$account->set('field_twitter', isset($request['twitter'])? $request['twitter']:'');
		$account->save();
		
		return static::accountToJsonResponse($account);
	}
	
	public function usuario_password(Request $request) {
		$request = Json::decode($request->getContent());
		
		$recovery = isset($request['email']);
		$account = $recovery? user_load_by_mail($request['email']):User::load($request['uid']);
		if(!$account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('No se encontró una cuenta con este correo electrónico.')]);
		
		$account->setPassword($request['password']);
		$account->save();
		
		return static::accountToJsonResponse($account);
	}
	
	public function keywords(Request $request) {
			$keywords = [];
			
			$items = Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree('palabras_clave', 0, 1, false);
			foreach($items as $id => $item){
				if($item->status != 1) continue;
				
				$keywords[] = ['tid' => (int) $item->tid, 'label' => (string) $item->name];
			}
			
			$response = new CacheableJsonResponse(['keywords' => $keywords]);
			
			return $response;
		}
	
	public function usuario_nuevo(Request $request) {
		$request = Json::decode($request->getContent());
		
		$email = $request['email'];
		$account = user_load_by_mail($email);
		if($account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('Este correo electrónico ya cuenta con un registro.')]);
		
		$name = $request['name'];
		
		$account = User::create();
		$username = $name.' '.rand(10, 99);
		
		$account->setPassword($request['password']);
		$account->enforceIsNew();
		$account->setEmail($email);
		$account->setUsername($username);
		$account->addRole('public');
		if(intval($request['vip']) == 1) $account->addRole('parlamentario');
			
		$account->set('field_fullname', $name);
		$account->set('field_tel', isset($request['tel'])? $request['tel']:'');
		$account->set('field_country', isset($request['country'])? $request['country']:'');
		$account->set('field_institution', isset($request['institution'])? $request['institution']:'');
		$account->set('field_facebook', isset($request['facebook'])? $request['facebook']:'');
		$account->set('field_twitter', isset($request['twitter'])? $request['twitter']:'');
		
		$keywords = explode('|', $request['keywords']);
		$account->set('field_palabras_clave', $keywords);
		
		$account->activate();
		$account->save();
		
		return static::accountToJsonResponse($account);
	}
	
	public function usuario_posts(Request $request){
		$request = Json::decode($request->getContent());
			
		$account = User::load($request['uid']);
		if(!$account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del usuario no es válido.')]);
		
		$posts = [];
		$items = Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['uid' => $account->id(), 'type' => 'blog_post']);
		foreach($items as $id => $item){			
			$posts[] = ['id' => $id, 'fecha' => (int) $item->created->value, 'uid' => (int) $item->uid->value, 'title' => $item->title->value, 'body' => $item->body->value, 'comments' => count($item->field_blog_comments)];
		}
		return new JsonResponse(['posts' => $posts]);		
	}
	
	public function post_nuevo(Request $request) {
		$request = Json::decode($request->getContent());
		
		$account = User::load($request['uid']);
		if(!$account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del usuario no es válido.')]);
		
		$post = Node::create(['uid' => $request['uid'], 'type' => 'blog_post', 'title' => $request['title'], 'body' => ['value' => $request['body'], 'format' => 'basic_html']]);
		$post->enforceIsNew();
		$post->save();
		
		return new JsonResponse(['id' => $post->nid]);
	}
	
	public function post_comments(Request $request){
		$request = Json::decode($request->getContent());
			
		$post = Node::load($request['id']);
		if(!$post) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del post no es válido.')]);
		
		$cids = Drupal::entityTypeManager()->getStorage('comment')->getQuery('AND')->condition('entity_id', $post->id())->condition('entity_type', 'node')->sort('created', 'DESC')->execute();
		
		$comments = [];		
		foreach($cids as $cid) {
		 $comment = Comment::load($cid);
		
		 $comments[] = [
				 'id' => (int) $cid,
				 'uid' => (int) $comment->getOwnerId(),
				 'subject' => $comment->get('subject')->value,
				 'body' => $comment->get('comment_body')->value,
				 'fecha' => (int) $comment->get('created')->value
		 ];
		}
			
		return new JsonResponse(['comments' => $comments]);
	}
	
	public function post_comment(Request $request) {
		$request = Json::decode($request->getContent());
		
		$post = Node::load($request['id']);
		if(!$post) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del post no es válido.')]);
		
		$comment = Comment::create(['uid' => $request['uid'], 'subject' => 'Comentario', 'field_name' => 'field_blog_comments', 'entity_type' => 'node', 'entity_id' => $post->id(), 'comment_type' => 'comment', 'status' => 1, 'comment_body' => ['value' => $request['body'], 'format' => 'basic_html']]);			
		$comment->save();
		
		return new JsonResponse(['id' => $comment->id()]);
	}

	public function profile_get_image(Request $request) {
		$current_user = \Drupal::currentUser();
		$user = \Drupal\user\Entity\User::load($current_user->id());
		if($current_user->isAuthenticated()){
			$current_email = $current_user->getEmail();
			if (!$user->user_picture->isEmpty()) {
				$uri = $user->user_picture->entity->getFileUri();
				$displayImg = file_create_url($uri);
			}else{
				$displayImg = '';
			}
			return new JsonResponse([
				'email'			=> $current_email,
				'displayImage' 	=> $displayImg
			]);
		}
	}

	public function profile_edit_image(Request $request) {
		$account = User::load($request->get('uid'));

		if(!$account) return new JsonResponse(['error_id' => 4, 'error_mensaje' => new TranslatableMarkup('El ID del usuario no es válido.')]);
		if($_FILES['picture']['error'] != 4){
			$temp_name = $_FILES['picture']['tmp_name'];
			$picture_type = $_FILES['picture']['type'];
			$picture_name = $_FILES['picture']['name'];
			$target = 'public://pictures/'.$picture_name;
			$data = file_get_contents($temp_name);
			$file = file_save_data($data, $target, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
			$file->save();
			$fid = $file->id();
			$account->set('user_picture', $fid);
			$account->save();
		}else{
			return new JsonResponse(['status' => 404,'message' => 'Por favor agregue una imagen']);
		}
		
		return static::accountToJsonResponse($account);
	}



	public function app_get_doc_list()
	{
		$videos = \Drupal::entityQuery('node')->condition('type', 'video')
		->execute();
		if (!empty($videos)) {
			$nodes = \Drupal\node\Entity\Node::loadMultiple($videos);
			
			foreach ($nodes as $node){
				$nid = $node->id();

				$video_id = $nid;
				$video_title = $node->get('title')->value;
				$video_summary = str_replace(array("\n", "\r"), '', strip_tags($node->get('body')->value));
				$author_id = $node->getOwnerID();
				$author_details = \Drupal\user\Entity\User::load($author_id);
				$video_author = $author_details->name->value;
				if (!$node->field_thumbnail->isEmpty()) {
					$uri = $node->field_thumbnail->entity->getFileUri();
					$thumb_url = file_create_url($uri);
				}else{
					$thumb_url = '';
				}
				$url = $node->field_upload_video->entity->getFileUri();
				$video_url = file_create_url($url);
				$video_value[] = array(
					'id' => $video_id,
					'title' => $video_title,
					'summary' => $video_summary,
					'author' => $video_author,
					'thumbnail' => $thumb_url,
					'video_url' => $video_url,
					'type' => 'video'
				);
			}
		}
		$pdf = \Drupal::entityQuery('node')->condition('type', 'pdf')
		->execute();
		if (!empty($pdf)) {
			$nodes = \Drupal\node\Entity\Node::loadMultiple($pdf);
	
			foreach ($nodes as $node){
				$nid = $node->id();

				$pdf_id = $nid;
				$pdf_title = $node->get('title')->value;
				$pdf_summary = str_replace(array("\n", "\r"), '', strip_tags($node->get('body')->value));
				$author_id = $node->getOwnerID();
				$author_details = \Drupal\user\Entity\User::load($author_id);
				$pdf_author = $author_details->name->value;
				if (!$node->field_pdf_thumbnail->isEmpty()) {
					$uri = $node->field_pdf_thumbnail->entity->getFileUri();
					$thumb_url = file_create_url($uri);
				}else{
					$thumb_url = '';
				}
				$url = $node->field_upload_pdf->entity->getFileUri();
				$pdf_url = file_create_url($url);
				$pdf_value[] = array(
					'id' => $pdf_id,
					'title' => $pdf_title,
					'summary' => $pdf_summary,
					'author' => $pdf_author,
					'thumbnail' => $thumb_url,
					'video_url' => $pdf_url,
					'type' => 'pdf'
				);
			}
		
		}
		if(!empty($video_value) || !empty($pdf_value)){
			return new JsonResponse(['videos' => $video_value, 'pdf' => $pdf_value]);
		}
		return new JsonResponse(['status' => 404, 'message' => 'No record found!']);
	}
		
}