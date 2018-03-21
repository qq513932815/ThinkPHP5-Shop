            //判断是否有%field%条件
            if(isset($filter['%field%']) && ''!=$filter['%field%'])
            {
            $model->where('%field%','like','%'.$filter['%field%'].'%');
            $filter_order['filter[%field%]'] = $filter['%field%'];
            }