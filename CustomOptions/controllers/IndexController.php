<?php

class Matheus_CustomOptions_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $url = $this->getUrl('custom_options/start/index');
        $urlValue = Mage::getSingleton('core/session')->getFormKey();
        $categories = $this->getCategories();

        $block_content = "
        <h2>Opções personalizadas</h2>
        <h4>Adicione opções personalizadas a produtos de uma categoria</h4>
        <h2>Geral</h2>
        <form action='$url' method='post'>
            <h4>Categoria onde os produtos se encontram:</h4>
            <select name='categories'>";
            
        foreach($categories as $id=>$category){
            $block_content .= "<option value='$id'>$category</option>";
        }     
        $block_content .= "
            </select>
            <br><br>
            <div style='display: flex; width: 100%'>
                <div style='width: 10%'>
                    <h4>Título</h4>
                    <input type='text' name='title' style='width: 70%'>
                </div>
                <div style='width: 10%'>
                    <h4>Tipo de campo:</h4>
                    <select name='field_type'>
                        <optgroup label='Texto'>
                            <option value='field'>Campo</option>
                            <option value='area'>Área</option>
                        </optgroup>
                        <optgroup label='Arquivo'>
                            <option value='file'>Arquivo</option>
                        </optgroup>
                        <optgroup label='Selecionar'>
                            <option value='drop_down'>Combobox</option>
                            <option value='radio'>Radio Buttons</option>
                            <option value='checkbox'>Checkbox</option>
                            <option value='multiple'>Múltiplas Seleções</option>
                        </optgroup>
                        <optgroup label='Data'>
                            <option value='date'>Data</option>
                            <option value='date_time'>Data &amp; Hora</option>
                            <option value='time'>Hora</option>
                        </optgroup>
                    </select>
                </div>
                <div style='width: 10%'>
                    <h4>Obrigatório?</h4>
                    <select name='is_require'>
                        <option value='1'>Sim</option>
                        <option value='0'>Não</option>
                    </select>
                </div>
            </div>
            <br><br>
            <div style='width: 30%;'>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Preço</th>
                            <th>Tipo de Preço</th>
                            <th>Ordem</th>
                        </tr>
                    </thead>
                    <tbody id='custom-options'></tbody>
                </table>
            </div>
            <br>
            <div id='add-line'>+ LINHA</div>
            <br><br>
            <input type='hidden' name='form_key' value='$urlValue'>
            <input type='submit' class='btn-default' id='submit' value='Começar'>
        </form>
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
        <script>
            jQuery.noConflict();
            function deleteRow(source) {
                    var row = source.parentNode;
                    row.remove();
            }
            jQuery(function ($) {
                $('#add-line').click(function(){
                    $('#custom-options').append(`
                        <tr>
                        <td><input type='text' name='option_titles[]'></td>
                        <td><input type='text' name='option_prices[]'></td>
                        <td>
                            <select name='option_price_types[]'>
                                <option value='fixed'>Fixo</option>
                                <option value='percentage'>Porcentagem</option>
                            </select>
                        </td>
                        <td><input type='text' name='option_orders[]'></td>
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
        th {
            border: 1px solid #333;
            text-align: center;
            padding: 1%;
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

    private function getCategories(){
        $category = Mage::getModel('catalog/category');
		$catTree = $category->getTreeModel()->load();
		$catIds = $catTree->getCollection()->getAllIds();
		if ($catIds){
            $catNames = array();
			foreach ($catIds as $id){
				$cat = Mage::getModel('catalog/category');
				$cat->load($id);
                $catNames[$id] = $cat->getName();
			} 
            return $catNames;
		} 
    }
}
