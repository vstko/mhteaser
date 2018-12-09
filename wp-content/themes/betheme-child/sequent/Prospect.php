<?php

class Prospect
{
    const SEQUENT_MAILCHIMP_PROPERTY_MAPPINGS = [
        'FNAME' => 'firstname',
        'LNAME' => 'lastname',
        'EMAIL' => 'email',
        'MMERGE3' => 'zip',
        'PHONE' => 'cellphone',
        'MMERGE5' => 'question_pricerange',
        'MMERGE6' => 'question_hearfrom',
        'MMERGE8' => 'none',
    ];

    const SEQUENT_MAILCHIMP_VALUE_MAPPINGS = [
        'question_pricerange' => [
            '800000' => 40562,
            '1200000' => 40563,
            '1975000' => 40564,
            '2600000' => 40565,

        ],
        'question_hearfrom' => [
            'News Articles/Press' => 40429,
            'Online Search' => 40431,
            'Broker' => 40425,
            'Family/Friend Referral' => 40434,
            'Live In The Neighborhood' => 40432, // mapped to other since this doesnt exist
        ]
    ];

    const SEQUENT_DEFAULT_CLIENT_TYPE = 40413;

    /**
     * @var integer
     */
    private $client_type;

    /**
     * @var string|null
     */
    private $address;

    /**
     * @var string|null
     */
    private $city;

    /**
     * @var string|null
     */
    private $state;

    /**
     * @var string|null
     */
    private $comments;

    /**
     * @var boolean|null
     */
    private $hasbroker;

    /**
     * @var boolean|null
     */
    private $sellfirst;

    /**
     * @var boolean|null
     */
    private $rentown;

    /**
     * @var string|null
     */
    private $realtor_name;

    /**
     * @var string|null
     */
    private $realtor_email;

    /**
     * @var integer|null
     */
    private $realtor_phone;

    /**
     * @var string|null
     */
    private $brokerage_company;

    /**
     * @var integer|null
     */
    private $question_whenbuy;

    /**
     * @var integer|null
     */
    private $question_whybuy;

    /**
     * @var integer|null
     */
    private $question_floorplans;

    /**
     * @var integer|null
     */
    private $question_publications;

    /**
     * @var integer|null
     */
    private $homephone;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var integer|null
     */
    private $zip;

    /**
     * @var integer|null
     */
    private $cellphone;

    /**
     * @var integer|null
     */
    private $question_pricerange;

    /**
     * @var integer|null
     */
    private $question_hearfrom;

    /**
     * @return int
     */
    public function getClientType(): int
    {
        return $this->client_type;
    }

    /**
     * @param int $client_type
     * @return self
     */
    public function setClientType(int $client_type)
    {
        $this->client_type = $client_type;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param null|string $address
     * @return self
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param null|string $city
     * @return self
     */
    public function setCity(string $city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param null|string $state
     * @return self
     */
    public function setState(string $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @param null|string $comments
     * @return self
     */
    public function setComments(string $comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasbroker(): bool
    {
        return $this->hasbroker;
    }

    /**
     * @param bool|null $hasbroker
     * @return self
     */
    public function setHasbroker(bool $hasbroker)
    {
        $this->hasbroker = $hasbroker;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSellfirst(): bool
    {
        return $this->sellfirst;
    }

    /**
     * @param bool|null $sellfirst
     * @return self
     */
    public function setSellfirst(bool $sellfirst)
    {
        $this->sellfirst = $sellfirst;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRentown(): bool
    {
        return $this->rentown;
    }

    /**
     * @param bool|null $rentown
     * @return self
     */
    public function setRentown(bool $rentown)
    {
        $this->rentown = $rentown;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRealtorName(): string
    {
        return $this->realtor_name;
    }

    /**
     * @param null|string $realtor_name
     * @return self
     */
    public function setRealtorName(string $realtor_name)
    {
        $this->realtor_name = $realtor_name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRealtorEmail(): string
    {
        return $this->realtor_email;
    }

    /**
     * @param null|string $realtor_email
     * @return self
     */
    public function setRealtorEmail(string $realtor_email)
    {
        $this->realtor_email = $realtor_email;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRealtorPhone(): int
    {
        return $this->realtor_phone;
    }

    /**
     * @param int|null $realtor_phone
     * @return self
     */
    public function setRealtorPhone(int $realtor_phone)
    {
        $this->realtor_phone = $realtor_phone;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBrokerageCompany(): string
    {
        return $this->brokerage_company;
    }

    /**
     * @param null|string $brokerage_company
     * @return self
     */
    public function setBrokerageCompany(string $brokerage_company)
    {
        $this->brokerage_company = $brokerage_company;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionWhenbuy(): int
    {
        return $this->question_whenbuy;
    }

    /**
     * @param int|null $question_whenbuy
     * @return self
     */
    public function setQuestionWhenbuy(int $question_whenbuy)
    {
        $this->question_whenbuy = $question_whenbuy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionWhybuy(): int
    {
        return $this->question_whybuy;
    }

    /**
     * @param int|null $question_whybuy
     * @return self
     */
    public function setQuestionWhybuy(int $question_whybuy)
    {
        $this->question_whybuy = $question_whybuy;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionFloorplans(): int
    {
        return $this->question_floorplans;
    }

    /**
     * @param int|null $question_floorplans
     * @return self
     */
    public function setQuestionFloorplans(int $question_floorplans)
    {
        $this->question_floorplans = $question_floorplans;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionPublications(): int
    {
        return $this->question_publications;
    }

    /**
     * @param int|null $question_publications
     * @return self
     */
    public function setQuestionPublications(int $question_publications)
    {
        $this->question_publications = $question_publications;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHomephone(): int
    {
        return $this->homephone;
    }

    /**
     * @param int|null $homephone
     * @return self
     */
    public function setHomephone(int $homephone)
    {
        $this->homephone = $homephone;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return self
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return self
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getZip(): int
    {
        return $this->zip;
    }

    /**
     * @param int|null $zip
     * @return self
     */
    public function setZip(int $zip)
    {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCellphone(): int
    {
        return $this->cellphone;
    }

    /**
     * @param int|null $cellphone
     * @return self
     */
    public function setCellphone(int $cellphone)
    {
        $this->cellphone = $cellphone;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionPricerange(): int
    {
        return $this->question_pricerange;
    }

    /**
     * @param int|null $question_pricerange
     * @return self
     */
    public function setQuestionPricerange(int $question_pricerange)
    {
        $this->question_pricerange = $question_pricerange;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuestionHearfrom(): int
    {
        return $this->question_hearfrom;
    }

    /**
     * @param int|null $question_hearfrom
     * @return self
     */
    public function setQuestionHearfrom(int $question_hearfrom)
    {
        $this->question_hearfrom = $question_hearfrom;
        return $this;
    }

    public function translateValuesFromMailchimpForm($mailchimpData)
    {
        $sequentEquivalentArr = [];
        foreach ($mailchimpData as $key => $val) {
            $sequentKeyExists = array_key_exists($key, self::SEQUENT_MAILCHIMP_PROPERTY_MAPPINGS);
            if($sequentKeyExists !== false){
                $sequentKey = self::SEQUENT_MAILCHIMP_PROPERTY_MAPPINGS[$key];
                if($sequentKey !== 'none') {
                    $sequentEquivalentArr[$sequentKey] = $val;
                }
            }
        }

        return $sequentEquivalentArr;
    }

    public function getDataToAddProspect($mailchimpData) {
        $sequentEquivalentArr = $this->translateValuesFromMailchimpForm($mailchimpData);

        $sequentEquivalentArr['question_pricerange'] = self::SEQUENT_MAILCHIMP_VALUE_MAPPINGS['question_pricerange'][(string)$sequentEquivalentArr['question_pricerange']];
        $sequentEquivalentArr['question_hearfrom'] = self::SEQUENT_MAILCHIMP_VALUE_MAPPINGS['question_hearfrom'][(string)$sequentEquivalentArr['question_hearfrom']];
        $sequentEquivalentArr['client_type'] = self::SEQUENT_DEFAULT_CLIENT_TYPE;

        return $sequentEquivalentArr;

    }

}