<?php

namespace DLM;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;

/**
 * class for generating the html 
 */
Class DLMBlock_Generator {

    public static function pfx($text) {
        $arr = explode(' ', $text);
        $text = join(' dlm-', $arr);
        return 'dlm-' . $text;
    }

    public static function generate_list_item($data) {
        return $data;
    }

    public static function generate($dlm_instance) {
        $doc = new \DOMDocument();

        $block = $doc->createElement('div');
        $block->setAttribute('class','dlm-block ' . $dlm_instance->dlm_id);
        $block->setAttribute('id',$dlm_instance->dlm_id);

        $container = $doc->createElement('div');
        $container->setAttribute('class', DLMBlock_Generator::pfx('container'));

        $row_1 = $doc->createElement('div');
        $row_1->setAttribute('class', DLMBlock_Generator::pfx('row'));
        
        $col_1 = $doc->createElement('div');
        $col_1->setAttribute('class', DLMBlock_Generator::pfx('col version'));

        $header = $doc->createElement('h5', 'VERSION');

        $radio_group = $doc->createElement('div');
        $radio_group->setAttribute('class', DLMBlock_Generator::pfx('radio-group'));
        $radio_group->setAttribute('name', 'version-select');

        $radio_options = [
            [
                'title' => 'Gold',
                'value' => 'gold'
            ],
        ];

        foreach ($radio_options as $opt) {
            $radio = $doc->createElement('label', $opt['title']);
            $radio->setAttribute('class', DLMBlock_Generator::pfx('radio') . ' ' . DLMBlock_Generator::pfx('selected'));
            $radio->setAttribute('value',$opt['value']);
            $radio->setAttribute('for','version-select--'.$opt['value']);
            $radio_group->appendChild($radio);
        }

        $subtext = $doc->createElement('p');
        $link = $doc->createElement('a', 'Version Archive');
        $link->setAttribute('href','');
        $break = $doc->createElement('br');

        $subtext->append('Visit the ', $link, ' to', $break,'access previous releases.');

        $col_1->append($header, $radio_group, $subtext);

        $col_2 = $doc->createElement('div');
        $col_2->setAttribute('class', DLMBlock_Generator::pfx('col os'));

        $header = $doc->createElement('h5', 'OPERATING SYSTEM');

        $select = $doc->createElement('select');
        $select->setAttribute('name','os-select');

        $operating_systems = array_map(function($obj) {
            return [
                'title' => $obj->title,
                'value' => $obj->slug,
            ];
        } , $dlm_instance->os_list);

        $default_opt = $doc->createElement('option', '- Choose an OS -');
        $default_opt->setAttribute('disabled', 'disabled');
        $default_opt->setAttribute('selected', 'selected');
        $default_opt->setAttribute('value', '0');
        
        $select->appendChild($default_opt);

        foreach ($operating_systems as $opt) {
            $os_opt = $doc->createElement('option', $opt['title']);
            $os_opt->setAttribute('value', $opt['value']);
            $select->appendChild($os_opt);
        }

        $col_2->append($header, $select);
        $row_1->append($col_1, $col_2);

        $divider = $doc->createElement('div');
        $divider->setAttribute('class', DLMBlock_Generator::pfx('row divider'));

        $row_2 = $doc->createElement('div');
        $row_2->setAttribute('class', DLMBlock_Generator::pfx('row lists'));

        $col_1 = $doc->createElement('div');
        $col_2 = $doc->createElement('div');
        $col_3 = $doc->createElement('div');

        $col_1->setAttribute('class', DLMBlock_Generator::pfx('col platforms'));
        $col_2->setAttribute('class', DLMBlock_Generator::pfx('col tools'));
        $col_3->setAttribute('class', DLMBlock_Generator::pfx('col plugins'));

        $header = $doc->createElement('h5', 'PLATFORMS');
        $list = $doc->createElement('ul');
        $list->setAttribute('data-list-json', json_encode($dlm_instance->get_file_list('platform')));

        $col_1->append($header, $list);

        $header = $doc->createElement('h5', 'TOOLS');
        $list = $doc->createElement('ul');
        $list->setAttribute('data-list-json', json_encode($dlm_instance->get_file_list('tool')));
        $col_2->append($header, $list);

        $header = $doc->createElement('h5', 'PLUGINS');
        $list = $doc->createElement('ul');
        $list->setAttribute('data-list-json', json_encode($dlm_instance->get_file_list('plugin')));
        $col_3->append($header, $list);

        $row_2 ->append($col_1, $col_2, $col_3);

        $row_3 = $doc->createElement('div');
        $row_3->setAttribute('class', DLMBlock_Generator::pfx('row action'));

        $col = $doc->createElement('div');
        $col->setAttribute('class', DLMBlock_Generator::pfx('col download'));

        $btn = $doc->createElement('button');
        $btn->setAttribute('class', DLMBlock_Generator::pfx('download-btn'));
        $btn->textContent = "DOWNLOAD";

        $col->appendChild($btn);

        $row_3->appendChild($col);

        $container->appendChild($row_1);
        $container->appendChild($divider);
        $container->appendChild($row_2);
        $container->appendChild($row_3);

        $block->appendChild($container);

        $doc->appendChild($block);

        $htmlString = $doc->saveHTML();
        echo $htmlString;
    }
}