<?php
/**
 * @file
 * Contains \Drupal\student_registration\Form\RegistrationForm.
 */
namespace Drupal\crud\Form;

use Drupal\Core\Form\FormBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class RegistrationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'resume_form';
  }

   /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $requestStack = \Drupal::service('request_stack');
    $currentRequest = $requestStack->getCurrentRequest();
    $paramValue = $currentRequest->query->get('node_id');
    $nodeValue = Node::load($paramValue);
    

    $form['candidate_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Candidate Name:'),
      // '#required' => TRUE,
    );

    if (!empty($nodeValue)) {
      $form['candidate_name']['#default_value'] = $nodeValue->title->value ?? "" ;
    }

    $form['field_hidden_id'] = array(
      '#type' => 'hidden' ,
      '#value' => $paramValue ,
      // '#required' => TRUE, 
    );

    $form['candidate_mail'] = array(
      '#type' => 'email',
      '#title' => t('Email ID:'),
      // '#required' => TRUE,
    );

    
    if (!empty($nodeValue)) {
      $form['candidate_mail']['#default_value'] = $nodeValue->field_email_id->value ?? "" ;
    }


    $form['candidate_class'] = array(
      '#type' => 'textfield',
      '#title' => t('Class:'),
      // '#required' => TRUE,
    );

    if (!empty($nodeValue)) {
      $form['candidate_class']['#default_value'] = $nodeValue->field_class->value ?? "" ;
    }


    $form['candidate_number'] = array (
      '#type' => 'tel',
      '#title' => t('Mobile no'),
    );

    if (!empty($nodeValue)) {
      $form['candidate_number']['#default_value'] = $nodeValue->field_mobile_no->value ?? "" ;
    }

    $form['candidate_dob'] = array (
      '#type' => 'date',
      '#title' => t('DOB'),
      // '#required' => TRUE,
    );

    if (!empty($nodeValue)) {
      $form['candidate_dob']['#default_value'] = $nodeValue->field_dob->value ?? "" ;
    }

    $form['candidate_gender'] = array (
      '#type' => 'select',
      '#title' => ('Gender'),
      '#options' => [
        'Female' => $this->t('Female'),
        'male' => $this->t('Male'),
      ],
    );

    if (!empty($nodeValue)) {
      $form['candidate_gender']['#default_value'] = $nodeValue->field_gender->value ?? "" ;
    }
    

    $form['user_profile'] = [
      '#title' => ('Profile'),
      '#type' => 'managed_file' ,
      '#required' => TRUE, 
      '#upload_location' => 'public://profile',
  ];

  if (!empty($nodeValue)) {
    $form['user_profile']['#default_value'] = $nodeValue->get("field_images")->target_id ?? "" ;
  }


    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
  //   return $form;

  //  // Gather the number of names in the form already.
  //  $num_names = $form_state->get('num_names');
  //  // We have to ensure that there is at least one name field.
  //  if ($num_names === NULL) {
  //    $name_field = $form_state->set('num_names', 1);
  //    $num_names = 1;
  //  }


  //  $form['#tree'] = TRUE;
  //  $form['candidate_product'] = array(
  //   '#type' => 'fieldset',
  //   '#title' => t('Subject:'),
  //   '#prefix' => '<div id="names-fieldset-wrapper">',
  //   '#suffix' => '</div>',
   
  // );

  //  for ($i = 0; $i < $num_names; $i++) {
  //    $form['candidate_product']['name'][$i] = [
  //      '#type' => 'textfield',
  //      '#title' => $this->t('Choose subject'),
  //    ];
  //  }

  //    if (!empty($nodeValue)) {
  //   $form['candidate_product']['#default_value'] = $nodeValue->field_gender->value ?? "" ;
  // }

  //  $form['candidate_product']['actions'] = [
  //    '#type' => 'actions',
  //  ];
  //  $form['candidate_product']['actions']['add_name'] = [
  //    '#type' => 'submit',
  //    '#value' => $this->t('Add one more'),
  //    '#submit' => ['::addOne'],
  //    '#ajax' => [
  //      'callback' => '::addmoreCallback',
  //      'wrapper' => 'names-fieldset-wrapper',
  //    ],
  //  ];

   // If there is more than one name, add the remove button.
  //  if ($num_names > 1) {
  //    $form['candidate_product']['actions']['remove_name'] = [
  //      '#type' => 'submit',
  //      '#value' => $this->t('Remove one'),
  //      '#submit' => ['::removeCallback'],
  //      '#ajax' => [
  //        'callback' => '::addmoreCallback',
  //        'wrapper' => 'names-fieldset-wrapper',
  //      ],
  //    ];
  //  }
  //  $form['actions']['submit'] = [
  //    '#type' => 'submit',
  //    '#value' => $this->t('Submit'),
  //  ];

  return $form;
  
  }

  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['candidate_product'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    // Since our buildForm() method relies on the value of 'num_names' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    // Since our buildForm() method relies on the value of 'num_names' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }
  
      /**
       * {@inheritdoc}
       */
      public function validateForm(array &$form, FormStateInterface $form_state) {

        if (strlen($form_state->getValue('candidate_number')) < 10) {
          $form_state->setErrorByName('candidate_number', $this->t('Mobile number is too short.'));
        }

      }

    public function submitForm(array &$form, FormStateInterface $form_state) 
    {
    

      $node_id = trim($form_state->getValue('field_hidden_id')); 
      $candidate_name = trim($form_state->getValue('candidate_name'));
      $candidate_class = trim($form_state->getValue('candidate_class'));
      $candidate_mail = trim($form_state->getValue('candidate_mail'));
      $candidate_number = ($form_state->getValue('candidate_number'));
      $candidate_dob = trim($form_state->getValue('candidate_dob'));
      $candidate_gender = $form_state->getValue('candidate_gender');  
      // $condidate_subject = $form_state->getValue(['candidate_product', 'name']);
      $pic = $form_state->getValue('user_profile')[0];
      $nodeValue = Node::load($node_id);
     
      // $sorted_Subjects =[];
      // $subject = $nodeValue->get("field_subject")->getValue();
      // dd($subject);   
      // foreach($subject as $each_subject){
      //     array_push($sorted_Subjects,$each_subject['value']);
      //   }
        // dump($sorted_Subjects);


      if (!empty($nodeValue)) {
        $nodeValue->title->value = $candidate_name;
        $nodeValue->field_email_id->value = $candidate_mail;
        $nodeValue->field_mobile_no->value = $candidate_number;
        $nodeValue->field_class->value = $candidate_class;
        $nodeValue->field_dob->value = $candidate_dob;
        $nodeValue->field_gender->value = $candidate_gender;
        $nodeValue->get("field_images")->target_id = $pic ;
        $nodeValue->save();
     
      }
      else{
        $node = Node::create([
          'type' => 'student',
          'title' => $candidate_name,
          'field_class' => $candidate_class,
          'field_email_id' => $candidate_mail ,
          'field_gender' => $candidate_gender ,
          'field_mobile_no' => $candidate_number ,
          'field_dob' => $candidate_dob ,
          'field_images' => $pic
        ]);
        $node->save();
      }
      $path = \Drupal\Core\Url::fromRoute('crud.view.dashboard')->toString();
      $response = new RedirectResponse($path);
      $response->send();
  }
}