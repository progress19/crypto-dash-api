<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ApiController extends Controller
{
    protected $binanceClient;

    public function __construct()
    {
        $key = env('BINANCE_API_KEY');
        $secret = env('BINANCE_API_SECRET');

        $this->binanceClient = new \Binance\Spot(['key' => $key, 'secret' => $secret]);
    }

    public function earnFlexibleList()
    {
        require_once '../vendor/autoload.php';

        $this->setCorsHeaders();
        
        $response = $this->binanceClient->earnFlexibleList();
                
        if (is_array($response) && !empty($response)) {
            if (isset($response['rows']) && !empty($response['rows'])) {
                $usdtLatestAnnualPercentageRate = null;
                foreach ($response['rows'] as $row) {
                    if ($row['asset'] === 'USDT') {
                        $usdtLatestAnnualPercentageRate = number_format($row['latestAnnualPercentageRate'] * 100, 2);
                        break;
                    }
                }
            } 
        } else {
            echo "La respuesta de la API no es un array o está vacía.";
        }

        return response()->json(['data' => $usdtLatestAnnualPercentageRate]);

    }

    public function getApyBorrowCoinTron($coin)
    {

        $url = 'https://openapi.just.network/lend/jtoken';

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $this->setCorsHeaders();

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Error decoding JSON response'], 500);
        }

        if (!isset($data['data']['tokenList'])) {
            return response()->json(['error' => 'Missing tokenList property in response'], 400);
        }

        $coinData = null;

        // Iterate and filter by coin symbol
        foreach ($data['data']['tokenList'] as $token) {
            if ($token['symbol'] === $coin) {
                $borrowRateInt = number_format($token['borrowRate'] * 100, 2);
                $coinData = [
                    'symbol' => $token['symbol'],
                    'borrowRate' => $borrowRateInt,
                ];
                break; // Exit loop once data is found
            }
        }

        // Handle case where coin is not found (optional)
        if (is_null($coinData)) {
            return response()->json(['error' => 'Coin not found'], 404);
        }

        return response()->json(['data' => $coinData]);
    }

    public function getBitcoinPrice()
    {

        $url = 'https://api.coindesk.com/v1/bpi/currentprice/BTC.json';

        // Realizar la solicitud HTTP para obtener los datos
        $response = file_get_contents($url);

        // Decodificar la respuesta JSON
        $data = json_decode($response);

        $precioBitcoin = $data->bpi->USD->rate; // Suponiendo que $precioBitcoin es una cadena que representa un número
        $precioBitcoinFloat = (float) str_replace(',', '', $precioBitcoin);

        // Formatea el número flotante con dos decimales
        $precioBitcoinFormatted = number_format($precioBitcoinFloat, 2);

        $this->setCorsHeaders();

        // Devolver el precio de Bitcoin como respuesta JSON
        return response()->json(['precio_bitcoin' => $precioBitcoinFormatted]);

    }

    public function accountSnapshot()
    {

        $response = $this->binanceClient->accountSnapshot('SPOT', ['recvWindow' => 5000]);
        dd($response);
    }

    public function loanFlexibleLoanableData()
    {

        $response = $this->binanceClient->loanFlexibleLoanableData(['loanCoin' => 'AXS',]);
        $this->setCorsHeaders();
        return response()->json(['data' => $response]);

    }

    protected function setCorsHeaders()
    {
        header('Access-Control-Allow-Origin: *');  // Not recommended for production
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    protected function setCorsHeadersX()
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'https://crypto-dash-front.vercel.app/',  // Add additional origins here
            'https://another-domain.com',
        ];
    
        if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        } else {
            // Handle case where origin is not allowed (optional)
        }
    
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
    

}
