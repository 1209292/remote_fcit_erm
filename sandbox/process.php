<?php
function search_1()
{
    $x = ['self improvement stays for life time|www.fcit.kau.edu.sa|2020|None',
        'improve your self before you have to|www.fcit.kau.edu.sa/goodone|2019|5',
        'kizon is contieous improvement|www.fcit.kau.edu.sa/perfect|2018|20',
        'I will live my life as I want, as I imagine|www.fcit.kau.edu.sa/goodSelfTalk|2017|30',
        'self respect, trust, everyday achieve|www.fcit.kau.edu.sa/talking fine|2016|35',
        'I will do it, Rizwan is proud, Iyad too|www.fcit.kau.edu.sa|2016|100',
        'This is what I want, This is what I\'m good at|www.IFindMe|Now|1000'];

    $y = $x[count($x) - 1];
    $count = count($y);
    for ($i = 0; $i < $count; $i++) {
        $queue = explode("|", $y[$i]);

        $item = ['title' => $queue[0],
            'url' => $queue[1],
            'year' => $queue[2],
            'citation' => $queue[3]
        ];
        $results[] = $item;
    }
    echo "search1";
    var_dump($results);
}

 function already_exists_test($scholar_pubs, $author_id){

     $item[] = ['title'     => 'I will live my life as I want, as I imagine',
                'url'       => 'www.fcit.kau.edu.sa/goodSelfTalk',
                'year'      => '2017',
                'citation'  => '30'
     ];
     $item[] = [
                 'title'    => 'kizon is contieous improvement',
                 'url'      => 'www.fcit.kau.edu.sa/perfect',
                 'year'     => '2018',
                 'citation' => '20'
     ];

    for($i=0; $i<count($scholar_pubs); $i++){
        foreach($item as $it){
            if($scholar_pubs[$i]['title'] == $it['title']){
                $scholar_pubs[$i] = array('dump') + $scholar_pubs[$i]; // add dump to top of array
            }
        }
    }

     var_dump($scholar_pubs);

     for($i=0; $i<count($scholar_pubs); $i++){
         if(current($scholar_pubs[$i]) == 'dump'){
             unset($scholar_pubs[$i]);
         }

     }
     echo "<br /><hr /><br />";
     var_dump($scholar_pubs);
     $scholar_pubs = array_values($scholar_pubs);
     echo "<br /><hr /><br />";
     var_dump($scholar_pubs);

}

function search_2(){
    $x = ['self improvement stays for life time|www.fcit.kau.edu.sa|2020|None',
        'improve your self before you have to|www.fcit.kau.edu.sa/goodone|2019|5',
        'kizon is contieous improvement|www.fcit.kau.edu.sa/perfect|2018|20',
        'I will live my life as I want, as I imagine|www.fcit.kau.edu.sa/goodSelfTalk|2017|30',
        'self respect, trust, everyday achieve|www.fcit.kau.edu.sa/talking fine|2016|35',
        'I will do it, Rizwan is proud, Iyad too|www.fcit.kau.edu.sa|2016|100',
        'This is what I want, This is what I\'m good at|www.IFindMe|Now|1000'];


    $count = count($x);
    for ($i = 0; $i < $count; $i++) {
        $queue = explode("|", $x[$i]);

        $item = ['title' => $queue[0],
            'url' => $queue[1],
            'year' => $queue[2],
            'citation' => $queue[3]
        ];
        $results[] = $item;
    }
    already_exists($results, 50);
}

    function search($author_full_name, $author_id)
{
    $x = ['self improvement stays for life time|www.fcit.kau.edu.sa|2020|None',
        'improve your self before you have to|www.fcit.kau.edu.sa/goodone|2019|5',
        'kizon is contieous improvement|www.fcit.kau.edu.sa/perfect|2018|20',
        'I will live my life as I want, as I imagine|www.fcit.kau.edu.sa/goodSelfTalk|2017|30',
        'self respect, trust, everyday achieve|www.fcit.kau.edu.sa/talking fine|2016|35',
        'I will do it, Rizwan is proud, Iyad too|www.fcit.kau.edu.sa|2016|100',
        'This is what I want, This is what I\'m good at|www.IFindMe|Now|1000'];

    $count = count($x); // so we can loop through each one, and if get to $count, we break from loop
    for ($j = 0; $j < $count; ++$j) {
        $q = explode("|", $x[$j]); // each pub in --csv is separated by (|), so we use it as delemeter
        $item = [
            'title'         => $q[0],
            'url'           => $q[1],
            'year'          => $q[2],
            'num_citations' => $q[3],
        ];
        $results[] = $item;
    }

//        foreach($results as $result){  // create objects of results instead of dealing with it as array
//            $sanitized_result [] = static::instantiate($result);
//        }
    $results = already_exists($results , $author_id);
    if(count($results) == 0 ){return false;} // all pub found is already in our DB
    $result = save($results);

//        echo "<pre>";
//        print_r($results);
//        echo "</pre>";
}

    function already_exists($scholar_pubs, $author_id){
        $publications[] = ['title'     => 'I will live my life as I want, as I imagine',
            'url'       => 'www.fcit.kau.edu.sa/goodSelfTalk',
            'year'      => '2017',
            'num_citations'  => '30'
        ];
        $publications[] = [
            'title'    => 'kizon is contieous improvement',
            'url'      => 'www.fcit.kau.edu.sa/perfect',
            'year'     => '2018',
            'num_citations' => '20'
        ];
    for($i=0; $i<count($scholar_pubs); $i++){
        foreach($publications as $publication){
            if($scholar_pubs[$i]['title'] == $publication['title']){
                $scholar_pubs[$i] = array('dump') + $scholar_pubs[$i]; // add dump to top of array
            }
        }
    }

    var_dump($scholar_pubs);

    for($i=0; $i<count($scholar_pubs); $i++){
        if(current($scholar_pubs[$i]) == 'dump'){
            unset($scholar_pubs[$i]);
        }

    }
    echo "<br /><hr /><br />";
    var_dump($scholar_pubs);
    $scholar_pubs = array_values($scholar_pubs);
    echo "<br /><hr /><br />";
    var_dump($scholar_pubs);
    return $scholar_pubs;
}

 function save($results){
    global $database;
    echo "<br />savefunction<hr /><br />";
    foreach($results as $result) {
        $sql = "INSERT INTO process (";
        $sql .= join(", ", array_keys($result));
        $sql .= ") VALUES ('";
        $sql .= "'{$result['title']}', '{$result['url']}', ";
        // title might not exists and in this case we will have (None) instead,
        // so we prepare the statement to to prevent error in quotes ('')
        if($result['year'] == 'None')
            $sql .= "'{$result['year']}'";
        else
            $sql .= "{$result['year']}";
        // same for num_citations
        if($result['num_citations'] == 'None')
            $sql .= "'{$result['num_citations']}'";
        else
            $sql .= "{$result['num_citations']}";
        $sql .= ")";
        var_dump($sql);
    }
}
search("Ali", 50);
?>