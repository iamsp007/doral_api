<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table='bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'account_name',
        'account_type',
        'routing_number',
        'account_number',
        'address_line_1',
        'address_line_2',
        'state',
        'city',
        'zip',
        'send_tax_documents_to',
        'legal_entity',
        'tax_payer_id_number'
    ];

    /**
     * Relation with state
     */
    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state', 'id');
    }

    /**
     * Relation with city
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city', 'id');
    }

    /**
     * Relation with city
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
