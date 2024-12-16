<?php
class Wapi{
    private $x_api_key;
    private $x_api_url;

    public function __construct($x_api_key,$x_api_url) {
        $this->x_api_key = $x_api_key;
         $this->x_api_url = $x_api_url;
    }

 public function generateImageId($filePath) {
     //echo $filePath; die;
        $curl = curl_init();
        //echo $this->x_api_url; die;
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->x_api_url.'/v1/whatsapp/media-id',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($filePath)),
          CURLOPT_HTTPHEADER => array(
            'x-api-key: '.$this->x_api_key
          ),
        ));

         $response = curl_exec($curl);
           if($response === false) {
    echo 'Curl error: ' . curl_error($curl);
}

        curl_close($curl);
           $responseData = json_decode($response, true);  
         
        // Check if the decoding was successful and the data field exists
if (isset($responseData['status']) && $responseData['status'] == 200 && isset($responseData['data']['id'])) {
    // Access the id value
    $imageId = $responseData['data']['id'];
   
} else {
    $imageId = '';
}
        
        return $imageId;
    }

    public function sendTemplateMessage($to, $name, $executiveName, $mobileNumber, $email, $imageId) {
        
         
        $jsonContent = '{
    "to": "'.$to.'",
    "data": {
        "name": "enquirycustomer",
        "language": {
            "code": "en"
        },
        "components": [
            {
                "type": "header",
                "parameters": [
                    {
                        "type": "image",
                        "image": {
                            "id": "'.$imageId.'"
                        }
                    }
                ]
            },
            {
                "type": "body",
                "parameters": [
                    {
                        "type": "text",
                        "text": "'.$name.'"
                    },
                    {
                        "type": "text",
                        "text": "'.$executiveName.'"
                    },
                    {
                        "type": "text",
                        "text": "'.$mobileNumber.'"
                    },
                    {
                        "type": "text",
                        "text": "'.$email.'"
                    }
                ]
            }
        ]
    }
}';
          
        $curl = curl_init();
     //echo $jsonContent; die;
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->x_api_url.'/v1/whatsapp/template/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>$jsonContent,
          CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json',
            'x-api-key: '.$this->x_api_key
          ),
          
        ));

         $response = curl_exec($curl); 
          //echo  $response; die;
           if($response === false) {
    echo 'Curl error: ' . curl_error($curl);
}
        curl_close($curl);
          $responseData = json_decode($response, true);  
           
        return $responseData;
    }
}


?>