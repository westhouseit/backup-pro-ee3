<?php
/**
 * mithra62 - Backup Pro
 *
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		3.0
 * @filesource 	./mithra62/BackupPro/Controllers/Eecms/Storage.php
 */

/**
 * Backup Pro - Eecms Restore Backup Controller
 *
 * Contains the Restore Backup Controller Actions for ExpressionEngine
 *
 * @package 	BackupPro\Eecms\Controllers
 * @author		Eric Lamb <eric@mithra62.com>
 */
trait BackupProStorageController
{
    /**
     * The default Storage form field values
     * @var unknown
     */
    public $storage_form_data_defaults = array(
        'storage_location_name' => '',
        'storage_location_file_use' => '1',
        'storage_location_status' => '1',
        'storage_location_db_use' => '1',
        'storage_location_include_prune' => '1',
    );
        
    /**
     * View all the storage entries 
     * @return string
     */
    public function view_storage()
    {
        $variables = array();
        $variables['can_remove'] = true;
        if( count($this->settings['storage_details']) <= 1 )
        {
            $variables['can_remove'] = false;
        }
    
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        $variables['storage_details'] = $this->settings['storage_details'];
        $variables['menu_data'] = ee()->backup_pro->get_settings_view_menu();
        $variables['section'] = 'storage';
        ee()->view->cp_page_title = $this->services['lang']->__('storage_bp_settings_menu');
        return array(
            'body' => ee()->load->view('storage', $variables, true),
            'heading' => $this->services['lang']->__($variables['section'].'_bp_settings_menu'),
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/backup_pro')->compile() => lang('backup_pro_module_name'),
                ee('CP/URL', 'addons/settings/backup_pro/settings/general')->compile() => lang('settings'),
            )
        );        
    }
    
    /**
     * Add a storage entry
     * @return string
     */
    public function new_storage($engine = 'local')
    {
        $variables = array();
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
    
        if( !isset($variables['available_storage_engines'][$engine]) )
        {
            $engine = 'local';
        }
        
        $variables['storage_details'] = $this->settings['storage_details'];    
        $variables['storage_engine'] = $variables['available_storage_engines'][$engine];
        $variables['form_data'] = array_merge($this->settings, $variables['storage_engine']['settings'], $this->storage_form_data_defaults);
        $variables['form_errors'] = array_merge($this->returnEmpty($this->settings), $this->returnEmpty($variables['storage_engine']['settings']), $this->storage_form_data_defaults);
    
        if( ee()->input->server('REQUEST_METHOD') == 'POST' )
        {
            $data = array();
            foreach($_POST as $key => $value){
                $data[$key] = ee()->input->post($key);
            }
            
            $variables['form_data'] = $data;
            $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $engine, $data, $this->settings['storage_details']);
            if( !$settings_errors )
            {
                if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->create($engine, $variables['form_data']) )
                {
                    ee()->session->set_flashdata('message_success', $this->services['lang']->__('storage_location_added'));
                    $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
                }
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }
    
        $variables['errors'] = $this->errors;
        $variables['_form_template'] = false;
        if( $variables['storage_engine']['obj']->hasSettingsView() )
        {
            $variables['_form_template'] = 'storage/drivers/_'.$engine;
        }

        $variables['menu_data'] = ee()->backup_pro->get_settings_view_menu();
        $variables['section'] = 'storage';
        $variables['engine'] = $engine;
        ee()->view->cp_page_title = $this->services['lang']->__('storage_bp_settings_menu');

        return array(
            'body' => ee()->load->view('storage/new', $variables, true),
            'heading' => $this->services['lang']->__('add_storage_location'),
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/backup_pro')->compile() => lang('backup_pro_module_name'),
                ee('CP/URL', 'addons/settings/backup_pro/settings/general')->compile() => lang('settings'),
                ee('CP/URL', 'addons/settings/backup_pro/view_storage')->compile() => $this->services['lang']->__('storage_bp_settings_menu'),
            )
        );
    }
    
    /**
     * Edit a storage entry
     * @return string
     */    
    public function edit_storage($storage_id)
    {
        if( empty($this->settings['storage_details'][$storage_id]) )
        {
            ee()->session->set_flashdata('message_error', $this->services['lang']->__('invalid_storage_id'));
            $this->platform->redirect($this->url_base.'view_storage');
        }
    
        $storage_details = $this->settings['storage_details'][$storage_id];
    
        $variables = array();
        $variables['storage_details'] = $storage_details;
        $variables['form_data'] = array_merge($this->storage_form_data_defaults, $storage_details);
        $variables['form_errors'] = $this->returnEmpty($storage_details); //array_merge($storage_details, $this->form_data_defaults);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageOptions();
        $variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
        $variables['_form_template'] = 'storage/drivers/_'.$storage_details['storage_location_driver'];
        
        if( ee()->input->server('REQUEST_METHOD') == 'POST' )
        {
            $data = array();
            foreach($_POST as $key => $value){
                $data[$key] = ee()->input->post($key);
            }
            
            $variables['form_data'] = $data;
            $data['location_id'] = $storage_id;
            $settings_errors = $this->services['backup']->getStorage()->validateDriver($this->services['validate'], $storage_details['storage_location_driver'], $data, $this->settings['storage_details']);
            if( !$settings_errors )
            {
                if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->update($storage_id, $variables['form_data']) )
                {
                    ee()->session->set_flashdata('message_success', $this->services['lang']->__('storage_location_updated'));
                    $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
                }
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }

        $variables['menu_data'] = ee()->backup_pro->get_settings_view_menu();
        $variables['section'] = 'storage';
        $variables['storage_id'] = $storage_id;
        ee()->view->cp_page_title = $this->services['lang']->__('storage_bp_settings_menu');
        //return ee()->load->view('storage/edit', $variables, true);

        return array(
            'body' => ee()->load->view('storage/edit', $variables, true),
            'heading' => $this->services['lang']->__('edit_storage_location'),
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/backup_pro')->compile() => lang('backup_pro_module_name'),
                ee('CP/URL', 'addons/settings/backup_pro/settings/general')->compile() => lang('settings'),
                ee('CP/URL', 'addons/settings/backup_pro/view_storage')->compile() => $this->services['lang']->__('storage_bp_settings_menu'),
            )
        );
    }
    
    /**
     * Remove a storage entry
     * @return string
     */    
    public function remove_storage($storage_id)
    {
        if( count($this->settings['storage_details']) <= 1 )
        {
            ee()->session->set_flashdata('message_error', $this->services['lang']->__('min_storage_location_needs'));
            $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
        }
    
        if( empty($this->settings['storage_details'][$storage_id]) )
        {
            ee()->session->set_flashdata('message_error', $this->services['lang']->__('invalid_storage_id'));
            $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
        }
    
        $storage_details = $this->settings['storage_details'][$storage_id];
    
        $variables = array();
        $variables['form_data'] = array('remove_remote_files' => '0');
        $variables['form_errors'] = array('remove_remote_files' => false);
        $variables['errors'] = $this->errors;
        $variables['available_storage_engines'] = $this->services['backup']->getStorage()->getAvailableStorageDrivers();
        $variables['storage_engine'] = $variables['available_storage_engines'][$storage_details['storage_location_driver']];
        $variables['storage_details'] = $storage_details;
    
        if( ee()->input->server('REQUEST_METHOD') == 'POST' )
        {
            $data = array();
            foreach($_POST as $key => $value){
                $data[$key] = ee()->input->post($key);
            }
            
            $backups = $this->services['backups']->setBackupPath($this->settings['working_directory'])
                                                  ->getAllBackups($this->settings['storage_details'], $this->services['backup']->getStorage()->getAvailableStorageDrivers());
    
            if( $this->services['backup']->getStorage()->getLocations()->setSetting($this->services['settings'])->remove($storage_id, $data, $backups) )
            {
                ee()->session->set_flashdata('message_success', $this->services['lang']->__('storage_location_removed'));
                $this->platform->redirect(ee('CP/URL', 'addons/settings/backup_pro/view_storage'));
            }
            else
            {
                $variables['form_errors'] = array_merge($variables['form_errors'], $settings_errors);
            }
        }

        $variables['menu_data'] = ee()->backup_pro->get_settings_view_menu();
        $variables['section'] = 'storage';
        $variables['storage_id'] = $storage_id;

        return array(
            'body' => ee()->load->view('storage/remove', $variables, true),
            'heading' => $this->services['lang']->__('remove_storage_location'),
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/backup_pro')->compile() => lang('backup_pro_module_name'),
                ee('CP/URL', 'addons/settings/backup_pro/settings/general')->compile() => lang('settings'),
                ee('CP/URL', 'addons/settings/backup_pro/view_storage')->compile() => $this->services['lang']->__('storage_bp_settings_menu'),
            )
        );        
    }
}