<?php
/**
 * Created by PhpStorm.
 * User: marcopfeiffer
 * Date: 01.07.14
 * Time: 10:49
 */

namespace Hn\BootstrapLayoutBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RteType extends AbstractType
{
    /**
     * Generated though the website (http://www.tinymce.com/wiki.php/Configuration) with following command:
     * var options = []; $('table.layout li').text(function(index,text){ options.push(text) }); options.join("','");
     *
     * @var string[]
     */
    private static $tinyMceOptions = array('auto_focus', 'directionality', 'browser_spellcheck', 'language', 'language_url', 'nowrap', 'object_resizing', 'plugins', 'external_plugins', 'selector', 'skin', 'skin_url', 'theme', 'theme_url', 'inline', 'hidden_input', 'convert_fonts_to_spans', 'custom_elements', 'doctype', 'element_format', 'entities', 'entity_encoding', 'extended_valid_elements', 'fix_list_elements', 'font_formats', 'fontsize_formats', 'force_p_newlines', 'force_hex_style_colors', 'forced_root_block', 'forced_root_block_attrs', 'formats', 'indentation', 'invalid_elements', 'invalid_styles', 'keep_styles', 'protect', 'schema', 'style_formats', 'block_formats', 'valid_children', 'valid_elements', 'valid_styles', 'valid_classes', 'body_id', 'body_class', 'content_css', 'visual', 'visual_table_class', 'visual_anchor_class', 'custom_undo_redo_levels', 'toolbar', 'toolbar<N>', 'menubar', 'menu', 'statusbar', 'resize', 'width', 'height', 'preview_styles', 'fixed_toolbar_container', 'event_root', 'convert_urls', 'relative_urls', 'remove_script_host', 'document_base_url', 'allow_script_urls', 'file_browser_callback', 'file_browser_callback_types', 'file_picker_callback', 'file_picker_types', 'color_picker_callback', 'init_instance_callback', 'setup', 'advlist_bullist_styles', 'advlist_numlist_styles', 'link_class_list', 'link_list', 'target_list', 'rel_list', 'link_title', 'image_class_list', 'image_list', 'image_advtab', 'image_description', 'image_dimensions', 'importcss_append', 'importcss_file_filter', 'importcss_selector_filter', 'importcss_groups', 'importcss_merge_classes', 'importcss_selector_converter', 'fullpage_default_doctype', 'fullpage_default_encoding', 'fullpage_default_title', 'fullpage_default_font_size', 'fullpage_default_font_family', 'fullpage_default_text_color', 'fullpage_default_langcode', 'fullpage_default_xml_pi', 'fullpage_hide_in_source_view', 'tabfocus_elements', 'table_clone_elements', 'table_grid', 'table_tab_navigation', 'table_default_attributes', 'table_default_styles', 'table_class_list', 'table_cell_class_list', 'table_row_class_list', 'table_adv_tab', 'table_cell_adv_tab', 'table_row_adv_tab', 'pagebreak_separator', 'visualblocks_default_state', 'paste_data_images', 'paste_as_text', 'paste_preprocess', 'paste_postprocess', 'paste_word_valid_elements', 'paste_webkit_styles', 'paste_retain_style_properties', 'paste_merge_formats', 'templates', 'template_cdate_classes', 'template_mdate_classes', 'template_cdate_format', 'template_mdate_format', 'template_selected_content_classes', 'template_replace_values', 'template_popup_width', 'template_popup_height', 'template_preview_replace_values', 'autosave_interval', 'autosave_restore_when_empty', 'autosave_retention', 'autoresize_min_height', 'autoresize_max_height', 'save_enablewhendirty', 'save_onsavecallback', 'save_oncancelcallback', 'noneditable_editable_class', 'noneditable_noneditable_class', 'noneditable_leave_contenteditable', 'insertdatetime_dateformat', 'insertdatetime_formats', 'insertdatetime_timeformat', 'insertdatetime_element', 'spellchecker_wordchar_pattern', 'spellchecker_rpc_url', 'spellchecker_language', 'spellchecker_languages', 'spellchecker_callback', 'contextmenu', 'media_alt_source', 'media_poster', 'media_dimensions', 'code_dialog_width', 'code_dialog_height', 'textpattern_patterns');

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $requestStack = $this->requestStack;
        $languageUrl = function (Options $options) use ($requestStack) {

            if ($options->has('lanugage') && is_string($options->get('language'))) {

                $language = $options['language'];
            } else {

                $request = $requestStack->getCurrentRequest();

                if ($request === null) {
                    throw new \RuntimeException("Rte type requires a language if outside of an request stack.");
                }

                $language = $request->getLocale();
            }

            return "/tinymce/langs/$language.js";
        };

        $resolver->setOptional(self::$tinyMceOptions);
        $resolver->setDefaults(array(
            'required' => false, // does not work properly

            // options of tinymce
            'language_url' => $languageUrl,
            'plugins' => 'autolink link paste legacyoutput',
            'skin_url' => '/tinymce/skins/light',
            'theme_url' => '/tinymce/themes/modern/theme.min.js',

            'statusbar' => false,
            'menubar' => false,
            'toolbar' => 'bold italic underline | bullist numlist | pastetext | link | undo | cleanup',

            'valid_elements' => 'b,i,u,ul,ol,li,a[href|target|title],p',
            'formats' => array(
                'bold' => array('inline' => 'b'),
                'italic' => array('inline' => 'i'),
                'underline' => array('inline' => 'u', 'classes' => 'underline', 'exact' => true)
            )
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // only use the options that are known for the mce
        $tinymceOptions = array_intersect_key($options, array_flip(self::$tinyMceOptions));
        $view->vars['tinymceOptions'] = $tinymceOptions;
    }

    public function getParent()
    {
        return 'textarea';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'hn_bootstrap_rte';
    }
}