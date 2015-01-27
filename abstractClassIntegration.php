<?php
/**
 * Entegrasyon için kullanacağımız abstract sınıfımız
 * User: Onur CANALP
 * Date: 19/01/15
 * Time: 10:44
 */
abstract class Integration
{
    private $xmlUrl             = "";
    public  $parsedXML          = false;
    public  $errorMessage       = "";
    public  $products           = array();
    public  $brandCampaignID    = 0; //ürünleri alınacak kategori veya kampanya
    public  $ourCampaignID      = 0; //ürünlerin aktarılacağı kampanya

    abstract protected function xml2Array();

    public function setXmlUrl($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL))
        {
            $this->errorMessage = "URL hatalı!";
            return false;
        }

        $this->xmlUrl = $url;
        return true;
    }

    public function setOurCampaignID($id)
    {
        if(intval($id) > 0)
        {
            $this->ourCampaignID = $id;
            return true;
        }
        else
        {
            $this->errorMessage = "HATA: Kampanya ID";
            return false;
        }
    }

    public function setBrandCampaignID($id)
    {
        if(intval($id) > 0)
        {
            $this->brandCampaignID = $id;
            return true;
        }
        else
        {
            $this->errorMessage = "HATA: Kategori ID";
            return false;
        }
    }

    public function getXML()
    {
        if($this->xmlUrl == ""){
            $this->errorMessage = "XML belirtilmemiş veya hatalı!";
            return false;
        }

        $contextHeader = array('http' => array('timeout' => 300));
        $context = stream_context_create($contextHeader);

        echo "XML indiriliyor";
        flush();
        $xml = file_get_contents($this->xmlUrl, false, $context);
        if(!$xml) {
            $this->errorMessage = "XML alınamadı";
            return false;
        }
        flush();
        echo "<span class=\"green\">[ OK ]</span><br />";
        flush();
        echo "<br />";
        flush();

        //Kampanya array olustur
        $parsedXML = @simplexml_load_string($xml);
        if(!$parsedXML) {
            $this->errorMessage = "XML alınamadı";
            return false;
        }

        $this->parsedXML = $parsedXML;
        flush();
        return true;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function hasError()
    {
        if($this->errorMessage != "")
            return true;
        else
            return false;
    }

    public function campaignControl()
    {
        global $db;

        if($this->ourCampaignID > 0)
        {
            $kampanya = $db->fetchRow("Kendinize uygun SQL inizi yazarsınız..", $this->ourCampaignID);
            if(!$kampanya) {
                $this->errorMessage = "Kampanya bulunamadı!";
                return false;
            }
            return true;
        }
        $this->errorMessage = "Kampanya kodu set edilmemiş veya hatalı!";
        return false;
    }

    public function installProducts($modul = "")
    {

        if(!$this->products) {
            $this->errorMessage = "Ürünler alınamadı";
            return false;
        }

        if(empty($modul)) {
            $this->errorMessage = "Modül alınamadı";
            return false;
        }

        if($this->products) {
            //Burada döngü açar içinde  ne yapmak istiyorsanız yaparsınız. $eklenenUrunler de adet tutabilirsiniz..
        }

        echo "<br /><em>(".$eklenenUrunler." ürün eklendi)</em><br />";
        flush();
        return true;
    }

    public function getWithCurl($url = "")
    {
        if(!filter_var($url, FILTER_VALIDATE_URL))
        {
            $this->errorMessage = "URL hatalı!";
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}