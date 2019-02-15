<?php
/**
 * @class Boletines
 * A simple application controller extension
 */
class Boletines extends ApplicationController {
    /**
     * view
     * Retrieves rows from database.
     */
    public function view() {
        $res = new Response();
        $res->success = true;
        $res->message = "Loaded data";
        $res->data = Boletin::all();
        return $res->to_json();
    }
    public function search() {
    	$res = new Response();
    	$res->success = true;
    	$res->message = "Loaded data";
//     	echo "ya entro al search";
//     	var_dump($this);
    	$res->data = Boletin::searchBoletines($this->parametros);
    	
    	return $res->to_json();
//     	return "";
    }
    /**
     * create
     */
    public function create() {
        $res = new Response();

        // data=<JSON encoded data>
        if ($this->params->data) {
        	if ($rec =  Boletin::create(json_decode($this->params->data, true))) {
                $res->success = true;
                $res->data = $rec->to_hash();
                $res->message = "Registro Creado!";
            } else {
                $res->success = false;
                $res->message = "Fallo al crear el registro";
            }
        }
        // Ugh, php...check if !hash
        else if (is_array($this->params) && !empty($this->params) && preg_match('/^\d+$/', implode('', array_keys($this->params)))) {
            foreach ($this->params as $data) {
            	array_push($res->data, Boletin::create($data)->to_hash());
            }
            $res->success = true;
            $res->message = "Se crearon " . count($res->data) . ' registros';
        }
        else {
        	if ($rec =  Boletin::create($this->params)) {
                $res->success = true;
                $res->data = $rec->to_hash();
                $res->message = "Registro Creado";
            } else {
                $res->success = false;
                $res->message = "Fallo al generar el registro";
            }
        }
        return $res->to_json();
    }

    /**
     * update
     */
    public function update() {
        $res = new Response();

        // data=<JSON encoded data>
        if ($this->params->data) {
            $data = json_decode($this->params->data, true);
            $res->data = array();
            foreach ($data as $rec_data) {
            	if ($rec = Boletin::update($rec_data->id, $rec_data)) {
                    array_push($res->data, $rec->to_hash());
                }
            }
            $res->success = true;
            $res->message = "Updated " . count($res->data) . " records!";
        }
        else if (is_array($this->params)) {
            $res->data = array();
            foreach ($this->params as $data) {
            	if ($rec = Boletin::update($data->id, $data)) {
                    array_push($res->data, $rec->to_hash());
                }
            }
            $res->success = true;
            $res->message = "Updated " . count($res->data) . " records";
        }
        else {
        	if ($rec = Boletin::update($this->params->id, $this->params)) {
                $res->data = $rec->to_hash();
                    $res->success = true;
                    $res->message = "Boletin actualizado";
//                }
            } else {
            	var_dump($this->params);
                $res->message = "Fallo al actualizar el Boletin " . $this->params->id;
                $res->success = false;
            }

        }
        return $res->to_json();
    }

    /**
     * destroy
     */
    public function destroy() {
        $res = new Response();

        // data=<JSON encoded data>
        if ($this->params->data) {
            $data = json_decode($this->params->data, true);
            $destroyed = array();
            foreach ($data as $rec_data) {
            	if ($rec = Boletin::destroy($rec_data->id)) {
                    array_push($destroyed, $rec->to_hash());
                }
            }
            $res->success = true;
            $res->message = "Eliminados " . count($destroyed) . " registros!";
        }
        else if (is_array($this->params)) {
            $destroyed = array();
            foreach ($this->params as $rec) {
            	if ($rec = Boletin::destroy($rec->id)) {
                    array_push($destroyed, $rec);
                }
            }
            $res->success = true;
            $res->message = 'Eliminados ' . count($destroyed) . ' registros';
        }
        else {
        	if ($rec = Boletin::destroy($this->params->id)) {
                $res->message = "Boletin eliminado";
                $res->success = true;
            } else {
            	var_dump($this->params);
                $res->message = "Fallo al eliminar el Boletin". $this->params->id;
            }
        }
        return $res->to_json();
    }
}

