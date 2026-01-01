<?php

namespace App\Parser;

class Markdown extends \Parsedown
{
    protected function inlineImage($Excerpt)
    {
        $data = parent::inlineImage($Excerpt);
        if (!is_array($data)) {
            return;
        }

        $data['element']['attributes']['class'] = 'img-fluid mx-auto d-block rounded img-size';

        $Inline = array(
            'extent' => $data['extent'],
            'element' => array(
                'name' => 'a',
                'handler' => 'element',
                'attributes' => array(
                    'href' => $data['element']['attributes']['src'],
                ),
                'text' => $data['element']
            ),
        );

        return $Inline;
    }

    protected function blockFencedCode($Line)
    {
        $data = parent::blockFencedCode($Line);
        if (!is_array($data)) {
            return;
        }

        $element = &$data['element']['text'];
        if (!isset($element['attributes'])) {
            $element['attributes'] = ['class' => ''];
        }

        $element['attributes']['class'] .= ' rounded';

        return $data;
    }

    protected function blockTable($Line, array $Block = null)
    {
        $data = parent::blockTable($Line, $Block);
        if (!is_array($data)) {
            return;
        }

        if($Block['element']['text'] === '|') {

            $data['element']['attributes'] = [
                'class' => 'table table-borderless'
            ];
            
            $data['element']['text'][0]['attributes'] = [
                'class' => 'd-none'
            ];
        } else {

            $data['element']['attributes'] = [
                'class' => 'table table-bordered'
            ];
            
            $data['element']['text'][0]['attributes'] = [
                'class' => 'table-secondary'
            ];    
        }
        

        return $data;
    }

    protected function blockHeader($Line)
    {
        $data = parent::blockHeader($Line);
        if (!is_array($data)) {
            return;
        }

        $name = $data['element']['name'];
        if (in_array($name, ['h1'])) {
            return $data;
        }

        $text = $data['element']['text'];

        $id = $name . '-' . str_replace(' ', '-', strtolower($text));

        $Inline = [
            'element' => [
                'name' => 'a',
                'handler' => 'element',
                'attributes' => [
                    'href' => '#' . $id,
                    'id' => $id,
                    'class' => 'text-decoration-none text-reset',
                ],
                'text' => $data['element']
            ],
        ];

        return $Inline;
    }

    protected function inlineLink($Excerpt)
    {
        $data = parent::inlineLink($Excerpt);
        if (!is_array($data)) {
            return;
        }

        $link = $data['element']['attributes']['href'];

        if (strpos($link, 'http') !== false) {
            $data['element']['attributes']['target'] = '_blank';
        }

        $data['element']['attributes']['class'] = 'link-primary link-underline-opacity-0 link-underline-opacity-100-hover';

        return $data;
    }

    protected function blockQuote($Line)
    {
        $data = parent::blockQuote($Line);
        if (!is_array($data)) {
            return;
        }

        $data['element']['attributes'] = [
            'class' => 'text-muted link-secondary quote'
        ];

        return $data;
    }

    protected function blockQuoteContinue($Line, array $Block)
    {
        $data = parent::blockQuoteContinue($Line, $Block);
        if (!is_array($data)) {
            return;
        }

        return $data;
    }
}
