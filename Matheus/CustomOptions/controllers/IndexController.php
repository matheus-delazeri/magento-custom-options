<?php

class Matheus_CustomOptions_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $start_url = $this->getUrl('custom_options/start/index');
        $temp_url = $this->getUrl('custom_options/temp/index');
        $categories = $this->getCategories();
        $loader_gif = "https://i.gifer.com/origin/b4/b4d657e7ef262b88eb5f7ac021edda87.gif";

        $block_content = "
        <h2>Custom options</h2>
        <h4>Add custom options in all products from a selected category.</h4>
        <h2>General</h2>
            <h4>Select the category:</h4>
            <select id='category'>";

        foreach ($categories as $id => $category) {
            $block_content .= "<option value='$id'>$category</option>";
        }
        $block_content .= "
            </select>
            <br><br>
            <div style='display: flex; width: 100%'>
                <div style='width: 10%'>
                    <h4>Title</h4>
                    <input type='text' id='title' style='width: 70%'>
                </div>
                <div style='width: 10%'>
                    <h4>Input Type:</h4>
                    <select id='field_type'>
                        <optgroup label='Text'>
                            <option value='field'>Field</option>
                            <option value='area'>Area</option>
                        </optgroup>
                        <optgroup label='File'>
                            <option value='file'>File</option>
                        </optgroup>
                        <optgroup label='Select'>
                            <option value='drop_down'>Drop-down</option>
                            <option value='radio'>Radio Buttons</option>
                            <option value='checkbox'>Checkbox</option>
                            <option value='multiple'>Multiple Select</option>
                        </optgroup>
                        <optgroup label='Date'>
                            <option value='date'>Date</option>
                            <option value='date_time'>Date &amp; Time</option>
                            <option value='time'>Time</option>
                        </optgroup>
                    </select>
                </div>
                <div style='width: 10%'>
                    <h4>Is required?</h4>
                    <select id='is_require'>
                        <option value='1'>Yes</option>
                        <option value='0'>No</option>
                    </select>
                </div>
            </div>
            <br><br>
            <div style='width: 30%;'>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Price Type</th>
                            <th>Sort Order</th>
                        </tr>
                    </thead>
                    <tbody id='custom-options'></tbody>
                </table>
            </div>
            <br>
            <div id='add-line'>+ Line</div>
            <br><br>
            <input type='submit' class='btn-default' id='start' value='Start'>
            <br><br>
            <div style='border:1px solid #ccc; border-radius: 5px; padding: 1% 2%; width: 20%; display: none;' id='log-div'>
                <div style='width: 80%;' id='progress'></div>
                <div style='width: 20%;' id='loader'></div>
            </div>
            <iframe id='loadarea' style='display:none;'></iframe><br />
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
        <script>
            jQuery.noConflict();
            function deleteRow(source) {
                    var row = source.parentNode;
                    row.remove();
            }
            
            jQuery(function ($) {
                function arrayToString(class_name){
                    el_str = '';
                    var elements = document.getElementsByClassName(class_name);
                    if(elements.lenght == 0){
                        return 'null';
                    }
                    for(var i = 0; i < elements.length; i++){
                        el_str += $(elements[i]).val() + ';';
                    }
                    return el_str.slice(0, -1);
                }
                $('#start').click(function(){
                    $('#log-div').css('display', 'flex');
                    $('#progress').empty();
                    $('#loader').append(`<img src='$loader_gif' style='float: right; width: 35%'>`);
                    $.ajax({
                        url: '".$temp_url."',
                        type: 'GET',
                        data: {
                            category : $('#category').val(),   
                            title : $('#title').val(),   
                            field_type : $('#field_type').val(),   
                            is_require : $('#is_require').val(),
                            option_titles : arrayToString('option_titles'),
                            option_prices : arrayToString('option_prices'),
                            option_price_types : arrayToString('option_price_types'),
                            option_orders : arrayToString('option_orders'),
                        },
                        success: function(result) {
                            document.getElementById('loadarea').src = '$start_url';
                        }
                    });
                });

                $('#add-line').click(function(){
                    $('#custom-options').append(`
                        <tr>
                        <td><input type='text' class='option_titles'></td>
                        <td style='width: 20%'><input type='text' class='option_prices'></td>
                        <td>
                            <select class='option_price_types'>
                                <option value='fixed'>Fixed</option>
                                <option value='percentage'>Percentage</option>
                            </select>
                        </td>
                        <td style='width: 15%'><input type='text' class='option_orders' style='width: 30%'></td>
                        <td class='btn-del' onclick='deleteRow(this)'><b>X</b></td>
                        </tr>
                    `);
                });
            });
        </script>
        
        <style type='text/css'>
        .btn-default{
            display: block;
            border: 0;
            width: 80px;
            background: #4E9CAF;
            padding: 5px 0%;
            text-align: center;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            line-height: 25px; 
        }
        .btn-del {
            width: 5%;
            text-align: center;
            border-radius: 15px;
            background: #ff0500;
            color: #fff;
            border-color: #fff;
        }
        .btn-del:hover {
            cursor: pointer;
        }
        table {
            width: 100%;
        }
        tbody {
            text-align: center;
        }
        th {
            border: 1px solid #333;
            text-align: center;
            padding: 1%;
            white-space: nowrap;
        }
        td {
            border: 1px solid #333;
            padding: 1%;
        }
        td > input, td > select {
            width: 90%;
        }
        #add-line {
            background: #4E9CAF;
            color: #fff;
            border-color: #fff;
            border-radius: 15px;
            width: 3%;
            font-weight: bold;
            text-align: center;
            padding: 0.5%;
        }
        #add-line:hover {
            cursor: pointer;
        }
        </style>";
        $this->loadLayout();

        $this->_setActiveMenu('catalog/matheus');
        $block = $this->getLayout()
            ->createBlock('core/text', 'export-block')
            ->setText($block_content);

        $this->_addContent($block);
        $this->renderLayout();
    }

    private function getCategories()
    {
        $category = Mage::getModel('catalog/category');
        $catTree = $category->getTreeModel()->load();
        $catIds = $catTree->getCollection()->getAllIds();
        if ($catIds) {
            $catNames = array();
            foreach ($catIds as $id) {
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $catNames[$id] = $cat->getName();
            }
            return $catNames;
        }
    }
}
