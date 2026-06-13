<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'key';

    /**
     * The "type" of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'logo',
        'default_member_markup',
        'default_reseller_markup',
        'popup_active',
        'popup_title',
        'popup_image',
        'popup_text',
        'popup_button_text',
        'popup_button_color',
        'popup_button_bg_color',
        'footer_text',
        'social_instagram',
        'social_telegram',
        'social_whatsapp',
    ];
}
