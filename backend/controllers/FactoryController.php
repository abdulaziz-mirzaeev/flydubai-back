<?php


namespace backend\controllers;


use backend\models\Cargo;
use backend\models\Client;
use backend\models\ClientType;
use backend\models\Company;
use backend\models\PackageType;
use backend\models\Tariff;
use backend\models\TariffType;
use backend\models\Ticket;
use backend\models\Tour;
use backend\models\TourOperator;
use backend\models\TourPackage;
use backend\models\TourPartner;
use Faker\Factory;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class FactoryController extends Controller
{
    public function actionClients($count = 1, $lang="ru_RU")
    {
        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $client = new Client();
            $gender = $faker->randomElement(['male', 'female']);
            $client->first_name = $faker->firstName($gender);
            $client->last_name = $faker->lastName($gender);
            $client->patronym = $faker->firstName('male') . '\'s child';
            $client->client_type_id = rand(1, 2);
            $client->passport_serial = strtoupper($faker->randomLetter . $faker->randomLetter);
            $client->passport_number = (string)$faker->randomNumber(7, true);
            $client->save();
            echo $client->id . ' '
                . $client->first_name . ' '
                . $client->last_name . '<br>';
        }

        echo "<h3>Success</h3>";
        echo "$count clients were created";

    }

    public function actionCompanies($count = 1, $lang="ru_RU")
    {
        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $company = new Company();
            $company->name = $faker->company;
            $company->save();
            echo $company->id . ' | ' . $company->name . '<br>';
        }

        echo "<h3>Success</h3>";
        echo "$count companies were created";
    }

    public function actionTickets($count = 1, $lang = "ru_RU", $save=true, $json = false)
    {
        $faker = Factory::create($lang);
        $tariff_ids = Tariff::find()
            ->select('id')
            ->asArray()
            ->all();
        $tariff_type_ids = TariffType::find()
            ->select('id')
            ->asArray()
            ->all();
        $client_ids = Client::find()
            ->select('id')
            ->asArray()
            ->all();

        for ($i = 1; $i <= $count; $i++) {
            $ticket = new Ticket();
            $ticket->flight_number = $faker->randomLetter
                . $faker->randomLetter . '-'
                . $faker->randomNumber(5);
            $ticket->flight_route = $faker->country;
            $ticket->cost_price = $faker->numberBetween(100, 1000);
            $ticket->sell_price = $ticket->cost_price + $faker->numberBetween(10, 300);
            $ticket->tariff_id = array_values( $faker->randomElement($tariff_ids) )[0];
            $ticket->tariff_type_id = array_values( $faker->randomElement($tariff_type_ids) )[0];
            $ticket->pnr = (string) $faker->randomNumber(5);
            $ticket->client_id = array_values( $faker->randomElement($client_ids) )[0];
            $ticket->passenger_count = $faker->numberBetween(1,4);
            $ticket->class = $faker->randomElement(['A','AA','B','BB']);
            $ticket->comment = $faker->realText(25);
            $ticket->flight_date = $faker->dateTimeThisYear->format('Y-m-d H:i:s');
            $ticket->save();
            echo $ticket->flight_number . ' | '
                . $ticket->flight_route . ' | '
                . $ticket->client->name . ' | '
                . $ticket->class;
        }
        echo "<h3>Success</h3>";
        echo "$count ticket(s) were created";

    }

    public function actionTours($count = 1, $lang = "ru_RU")
    {
        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $tour = new Tour();
            $tour->name = $faker->country;
            $tour->created_at = date('Y-m-d h:i:s');
            $tour->save();
            if (!empty($tour->getErrors()))
                d($tour->getErrors());
        }

        echo "<h3>Success</h3>";
        echo "$count tours were created";
    }

    public function actionTouroperator($count = 1, $lang = "ru_RU")
    {
        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $tourOperator = new TourOperator();
            $tourOperator->name = "Тур Оператор: " . $faker->firstName;
            $tourOperator->save();
            if (!empty($tourOperator->getErrors()))
                d($tourOperator->getErrors());
        }

        echo "<h3>Success</h3>";
        echo "$count tours were created";
    }

    public function actionTourpartner($count = 1, $lang = "ru_RU")
    {
        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $tourPartner = new TourPartner();
            $tourPartner->name = $faker->company;
            $tourPartner->save();
            if (!empty($tourPartner->getErrors()))
                d($tourPartner->getErrors());
        }

        echo "<h3>Success</h3>";
        echo "$count tours were created";
    }

    /**
     * Returns collected ids of the given model
     * @param string $model
     * @param string $column
     */
    public function selectFrom($model, $column='id')
    {
        $array = $model::find()->asArray()->all();
        $array = ArrayHelper::getColumn($array, $column);
        return $array;
    }

    public function actionTourpackage($count = 1, $lang = "ru_RU")
    {
        $clientIds = $this->selectFrom(Client::class);
        $tourPartnerIds = $this->selectFrom(TourPartner::class);
        $tourOperatorIds = $this->selectFrom(TourOperator::class);
        $tourIds = $this->selectFrom(Tour::class);

        $faker = Factory::create($lang);

        for ($i = 1; $i <= $count; $i++) {
            $tourPackage = new TourPackage();
            $tourPackage->tour_operator_id = $faker->randomElement($tourOperatorIds);
            $tourPackage->tour_id = $faker->randomElement($tourIds);
            $tourPackage->tour_partner_id = $faker->randomElement($tourPartnerIds);
            $tourPackage->client_id = $faker->randomElement($clientIds);
            $tourPackage->cost_price = $faker->numberBetween(100, 1000);
            $tourPackage->sell_price = $tourPackage->cost_price + $faker->numberBetween(10, 300);

            $tourPackage->save();
            if (!empty($tourPackage->getErrors()))
                d($tourPackage->getErrors());
        }

        echo "<h3>Success</h3>";
        echo "$count tours were created";
    }

    public function actionCargo($count = 1, $lang = "ru_RU", $save=true, $json = false)
    {
        $faker = Factory::create($lang);

        $client_type_ids = $this->selectFrom(ClientType::class);
        $company_ids = $this->selectFrom(Company::class);
        $client_ids = $this->selectFrom(Client::class);
        $package_type_ids = $this->selectFrom(PackageType::class);

        for ($i = 1; $i <= $count; $i++) {
            $cargo = new Cargo();
            $cargo->air_waybill = $faker->randomLetter . $faker->randomNumber(5);
            $cargo->client_type_id = $faker->randomElement($client_type_ids);
            $cargo->client_id = $faker->randomElement($client_ids);
            $cargo->company_id = $faker->randomElement($company_ids);
            $cargo->package_amount = $faker->numberBetween(1,10);
            $cargo->package_weight = $faker->randomNumber(3);
            $cargo->package_type_id = $faker->randomElement($package_type_ids);
            $cargo->cost_price = $faker->numberBetween(100, 1000);
            $cargo->sell_price = $cargo->cost_price + $faker->numberBetween(10, 300);
            $cargo->save();
        }
        echo "<h3>Success</h3>";
        echo "$count cargo(s) were created";

    }




}