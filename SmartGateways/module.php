<?php

/**
 * WebsocketServer Klasse implementiert das Websocket-Protokoll für einen ServerSocket.
 * Erweitert IPSModule.
 *
 * @package       SmartGateways
 * @version       1.1
 *
 */
class IPSSmartGateways extends IPSModule
{

    private function __CreateVariable($Name, $Type, $Value, $Ident = '', $ParentID = 0)
    {
        //echo "CreateVariable: ( $Name, $Type, $Value, $Ident, $ParentID ) \n";
        if ('' != $Ident) {
            $VarID = @IPS_GetObjectIDByIdent($Ident, $ParentID);
            if (false !== $VarID) {
                $this->__SetVariable($VarID, $Type, $Value);
                return $VarID;
            }
        }
        $VarID = @IPS_GetObjectIDByName($Name, $ParentID);
        if (false !== $VarID) { // exists?
            $Obj = IPS_GetObject($VarID);
            if (2 == $Obj['ObjectType']) { // is variable?
                $Var = IPS_GetVariable($VarID);
                if ($Type == $Var['VariableValue']['ValueType']) {
                    $this->__SetVariable($VarID, $Type, $Value);
                    return $VarID;
                }
            }
        }
        $VarID = IPS_CreateVariable($Type);
        IPS_SetParent($VarID, $ParentID);
        IPS_SetName($VarID, $Name);
        if ('' != $Ident) {
            IPS_SetIdent($VarID, $Ident);
        }
        $this->__SetVariable($VarID, $Type, $Value);
        return $VarID;
    }

    private function __SetVariable($VarID, $Type, $Value)
    {
        switch ($Type) {
            case 0: // boolean
                SetValueBoolean($VarID, $Value);
                break;
            case 1: // integer
                SetValueInteger($VarID, $Value);
                break;
            case 2: // float
                SetValueFloat($VarID, $Value);
                break;
            case 3: // string
                SetValueString($VarID, $Value);
                break;
        }
    }
    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");
        $this->SetBuffer("Data", "");

        if (!IPS_VariableProfileExists("P1kWhProfile")) {
            IPS_CreateVariableProfile("P1kWhProfile", 2);
            IPS_SetVariableProfileDigits("P1kWhProfile", 2);
            IPS_SetVariableProfileText("P1kWhProfile", "", " kWh");
        }
        if (!IPS_VariableProfileExists("P1kWattProfile")) {
            IPS_CreateVariableProfile("P1kWattProfile", 1);

            IPS_SetVariableProfileText("P1kWattProfile", "", " Watt");
        }
        if (!IPS_VariableProfileExists("P1GasProfile")) {
            IPS_CreateVariableProfile("P1GasProfile", 2);
            IPS_SetVariableProfileDigits("P1GasProfile", 2);
            IPS_SetVariableProfileText("P1GasProfile", "", " m3");
        }
        if (!IPS_VariableProfileExists("P1TariefProfile")) {
            IPS_CreateVariableProfile("P1TariefProfile", 0);

            IPS_SetVariableProfileAssociation("P1TariefProfile", 0, "Dag tarief", "", -1);
            IPS_SetVariableProfileAssociation("P1TariefProfile", 1, "Nacht tarief", "", -1);
        }

        $id = $this->__CreateVariable("Verbuik Nacht", 2, 0, "P1VerbruikNacht", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWhProfile");
        $id = $this->__CreateVariable("Verbuik Dag", 2, 0, "P1VerbruikDag", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWhProfile");
        $id = $this->__CreateVariable("Opbrengst Nacht", 2, 0, "P1OpbrengstNacht", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWhProfile");
        $id = $this->__CreateVariable("Opbrengst Dag", 2, 0, "P1OpbrengstDag", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWhProfile");
        $id = $this->__CreateVariable("Huidig Vebruik", 1, 0, "P1HuidigVerbruik", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWattProfile");
        $id = $this->__CreateVariable("Huidig Opbrengst", 1, 0, "P1HuidigOpbrengst", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWattProfile");
        $id = $this->__CreateVariable("Totaal Vebruik", 1, 0, "P1TotaalVerbruik", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1kWattProfile");
        $id = $this->__CreateVariable("Actueel Tarief", 0, 0, "P1Tarief", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1TariefProfile");
        $id = $this->__CreateVariable("Gas vebruik", 2, 0, "P1Gas", $this->InstanceID);
        IPS_SetVariableCustomProfile($id, "P1GasProfile");

    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Destroy()
    {
        if (IPS_InstanceExists($this->InstanceID)) {

        }
        parent::Destroy();
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();

    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        $this->IOMessageSink($TimeStamp, $SenderID, $Message, $Data);

//        switch ($Message)
        //        {
        //            case IPS_KERNELSTARTED:
        //                $this->KernelReady();
        //                break;
        //        }
    }

    /**
     * Wird ausgeführt wenn sich der Status vom Parent ändert.
     * @access protected
     */
    protected function _IOChangeState($State)
    {
        if ($State == IS_ACTIVE) {

        } else {

        }
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function GetConfigurationForm()
    {
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function GetConfigurationForParent()
    {
        //115200 8N1
        $Config['StopBits'] = 1;
        $Config['BaudRate'] = 115200;
        $Config['Parity'] = 'None';
        $Config['DataBits'] = 8;
        return json_encode($Config);
    }

    public function ReceiveData($JSONString)
    {
        $data = json_decode($JSONString);

        $dt = utf8_decode($data->Buffer);
        $pos = strpos($dt, "!");

        $Data = $this->GetBuffer('Data');

        if ($pos === false) {
            $Data .= $dt;
            $this->SetBuffer("Data", $Data);

        } else {
            $Data = $Data . $dt;
            // IPS_LogMessage("P1Data", $Data);

            preg_match('/^(1-0:1\.8\.1\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
            $verbruiknacht = (float) (@$output_array[2]);
            //  IPS_LogMessage("P1Data", $verbruiknacht);
            $sid = @IPS_GetObjectIDByIdent("P1VerbruikNacht", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($verbruiknacht != $val) {
                    SetValue($sid, $verbruiknacht);
                }
            }

            preg_match('/^(1-0:1\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
            $verbruikdag = (float) (@$output_array[2]);
            // IPS_LogMessage("P1Data", $verbruikdag);

            $sid = @IPS_GetObjectIDByIdent("P1VerbruikDag", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($verbruikdag != $val) {
                    SetValue($sid, $verbruikdag);}
            }

            preg_match('/^(1-0:2\.8\.1\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
            $opbrengstnacht = (float) (@$output_array[2]);
            // IPS_LogMessage("P1Data", $opbrengstnacht);
            $sid = @IPS_GetObjectIDByIdent("P1OpbrengstNacht", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($opbrengstnacht != $val) {
                    SetValue($sid, $opbrengstnacht);
                }
            }

            preg_match('/^(1-0:2\.8\.2\((\d+\.\d+)\*kWh\))/m', $Data, $output_array);
            $opbrengstdag = (float) (@$output_array[2]);
            //  IPS_LogMessage("P1Data", $opbrengstdag);
            $sid = @IPS_GetObjectIDByIdent("P1OpbrengstDag", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($opbrengstdag != $val) {
                    SetValue($sid, $opbrengstdag);}
            }

            preg_match('/^(0-0:96\.14\.0\((\d+)\))/m', $Data, $output_array);
            $tarief = (int) (@$output_array[2]);
            // IPS_LogMessage("P1Data", $tarief);
            $sid = @IPS_GetObjectIDByIdent("P1Tarief", $this->InstanceID);
            if ($sid) {
                // fase Dag, true Nacht
                if ($tarief == 2) {
                    $trf = false;
                } else {
                    $trf = true;
                }

                $val = GetValue($sid);
                if ($trf != $val) {
                    SetValue($sid, $trf);}
            }

            preg_match('/^(1-0:1\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
            $huidigvebruik = (float) (@$output_array[2]) * 1000;
            // IPS_LogMessage("P1Data", $huidigvebruik);

            $sid = @IPS_GetObjectIDByIdent("P1HuidigVerbruik", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($huidigvebruik != $val) {
                    SetValue($sid, $huidigvebruik);}
            }

            preg_match('/^(1-0:2\.7\.0\((\d+.\d+)\*kW\))/m', $Data, $output_array);
            $huidigopbrengst = (float) (@$output_array[2]) * 1000;
            // IPS_LogMessage("P1Data", $huidigopbrengst);
            $sid = @IPS_GetObjectIDByIdent("P1HuidigOpbrengst", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($huidigopbrengst != $val) {
                    SetValue($sid, $huidigopbrengst);}
            }

            $huidigtotaal = $huidigvebruik - $huidigopbrengst;
            // IPS_LogMessage("P1Data", $huidigtotaal);
            $sid = @IPS_GetObjectIDByIdent("P1TotaalVerbruik", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($huidigtotaal != $val) {
                    SetValue($sid, $huidigtotaal);}
            }

            preg_match('/0-1:24\.2\.1\x28.*\x28([0-9]+\.[0-9]+).m3\)/', $Data, $output_array);
            $gasverbruik = (float) (@$output_array[1]);
            //IPS_LogMessage("P1Data", $gasverbruik);
            $sid = @IPS_GetObjectIDByIdent("P1Gas", $this->InstanceID);
            if ($sid) {
                $val = GetValue($sid);
                if ($gasverbruik != $val) {
                    SetValue($sid, $gasverbruik);}
            }

            $this->SetBuffer("Data", "");

        }

        return true;
    }

}
