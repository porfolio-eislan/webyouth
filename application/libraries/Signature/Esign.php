<?php

include 'Client/BasicRest.php';

class Esign
{

    public function checkStatus($nik = '')
    {
        $rest = new BasicRest();
        $data = ['nik' => $nik];
        date_default_timezone_set('Asia/Jakarta');
        $response = $rest->send('/api/v2/user/check/status', 'POST', $data);
        if (!$response) return $rest->getError();
        else return $response;
    }

    public function checkSign($file = '')
    {
        $rest = new BasicRest();
        $data = ['file' => base64_encode(file_get_contents($file))];

        date_default_timezone_set('Asia/Jakarta');
        $response = $rest->send('/api/v2/verify/pdf', 'POST', $data);
        if (!$response) return $rest->getError();
        else return $response;
    }

    public function sign($nik = '', $pass = '', $pdf = '', $rename = true)
    {
        $rest = new BasicRest();
        $ext = pathinfo($pdf, PATHINFO_EXTENSION);
        $filename = basename($pdf, '.' . $ext);
        $directoryName = realpath(dirname($pdf));

        $data = array(
            'nik' => $nik,
            'passphrase' => $pass,
            'signatureProperties' => [[
                'tampilan' => "INVISIBLE",
                "location" => 'SURAKARTA',
                "reason" => "Tanda Tangan Elekronik, RST Tk.III Slamet Riyadi"
            ]]
        );

        $files = array(
            'file' => [base64_encode(file_get_contents($pdf))],
        );

        date_default_timezone_set('Asia/Jakarta');
        $response = $rest->send('/api/v2/sign/pdf', 'POST', $data, $files);
        if (@$response['file'][0]) {
            $file_content = base64_decode($response['file'][0]);
            if ($rename) {
                file_put_contents($directoryName . '/' . $filename . '_signed.pdf', $file_content);
                unlink($pdf);
            } else {
                file_put_contents($directoryName . '/' . $filename . '.pdf', $file_content);
            }
            $response = [
                'status_code' => '200',
                'message' => 'TTE Berhasil!',
            ];
        } else {
            $response = [
                'status_code' => @$response['status_code'],
                'message' => @$response['error'],
            ];
        }
        return $response;
    }
}
