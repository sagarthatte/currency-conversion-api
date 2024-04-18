<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'request_data',
        'response_data',
        'status',
        'request_at',
        'response_at'
    ];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function storeRequest(User $user, array $requestData): self
    {
        $this->user_id = $user->id;
        $this->status = 'pending';  // Always mark new requests as pending
        $this->response_data = '';
        $this->type = $requestData['type'];
        $this->request_data = json_encode($requestData);
        $this->request_at = now();
        $this->response_at = null;
        $this->save();
        
        return $this;
    }
}
