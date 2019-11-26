<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenanceItems()
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function featuredImage()
    {
        return $this->belongsTo(Image::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function monthsDescription()
    {
        return $this->hasMany(MonthsDescription::class, 'months_id');
    }


    public function interval()
    {
        return $this->hasOne(Interval::class, 'id', 'interval_id');
    }
}
