<?php

namespace Drupal\crud\Controller;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

class CrudController
{
    public function displayNodeData()
    {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $nids = \Drupal::entityQuery('node')->condition('type','student')->execute();
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
        $student_data = [];
        foreach($nodes as $key => $values){
        $imageUrl = $values->field_images->entity->getFileUri();
        $url = \Drupal\image\Entity\ImageStyle::load('medium')->buildUrl($imageUrl);
            $student_data[] = [
				'nid' => $values->id(),
                'title' => $values->title->value,
                'email' => $values->field_email_id->value,
                'dob' => $values->field_dob->value,
                'genders' => $values->field_gender->value,
                'profile' =>  $url,
                'mobile' => $values->field_mobile_no->value,
                'class' => $values->field_class->value,
                // 'subject' => $values->get("field_subject")->getValue(),
            ];
        }
        return [
            '#theme' => 'displaydata' ,
            '#allstudentdata' => $student_data
        ];
    }


    public function DeleteData(Request $req)
{
    $id = $req->get("id");
    $node = Node::load($id);
    $node->delete();
    \Drupal::service('page_cache_kill_switch')->trigger();
    $path = \Drupal\Core\Url::fromRoute('crud.view.dashboard')->toString();
    return new RedirectResponse($path);
}

}