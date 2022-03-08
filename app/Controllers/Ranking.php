<?php
namespace App\Controllers;

use App\Core\Controller;

class Ranking extends Controller
{
    private $resultPerPage = RANKING_RESULTS_PER_PAGE;
    private $maxPages = RANKING_MAX_ALLOWED_PAGES;
    private $searchFor = "";
    private $page = 1;
    private $tabType = 0;

    public function index($arguments = [])
    {
        return $this->exp($arguments);
    }

    public function daily($arguments)
    {
        return $this->pvp($arguments, "daily");
    }

    public function weekly($arguments)
    {
        return $this->pvp($arguments, "weekly");
    }

    public function total($arguments)
    {
        return $this->pvp($arguments, "total");
    }

    public function exp($arguments)
    {
        $return = [];
        $return["Ranking"] = [];

        //Character page get
        if(isset($arguments[0]) && is_string($arguments[0]))
        {
            $this->tabType = getCharacterNumber(strtolower($arguments[0])) + 1;
        }

        //Page number get
        if(isset($arguments[1]) && is_numeric($arguments[1]))
        {
            $this->page = (int)$arguments[1];
            $this->page = ($this->page != 0) ? $this->page : 1;
        }

        //Gotocontainer
        if(isset($arguments[2]) && $arguments[2] == "container")
        {
            $return["gotoContainer"] = true;
        }

        $model = new \App\Models\Ranking();
        $total_results = $model->getRankingCount("exp", $this->tabType);
        $total_pages = ceil($total_results / $this->resultPerPage);
        if($total_pages > $this->maxPages)
        {
            $total_pages = $this->maxPages;
        }

        //Get search for
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST["search_nickname"]))
        {
            $return["gotoContainer"] = true;
            $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $this->searchFor = $form["search_nickname"];
            $search = $model->getRankingByUser("exp", $this->tabType, $this->searchFor);
            if(!empty($search))
            {
                $searchUserRank = $search->Rank;
                $this->page = ceil($searchUserRank / $this->resultPerPage);
            }
            else
            {
                $this->resultPerPage = 0;
                $total_pages = 1;
            }
        }

        //Verifies if (&page=) exceeds the page limit (Except if searched for character)
        if ($this->page > $total_pages && !isset($_POST['search_nickname'])) {
            $this->page = 1;
        }

        $max_block = ceil($this->page / 10) * 10;
        $start = ceil($this->resultPerPage * ($this->page - 1) + 1);
        $end = ceil($start + $this->resultPerPage - 1);
        
        //Assemble the ranking array
        $ranking = $model->getRanking("exp", $this->tabType, $start, $end);
        $userModel = new \App\Models\User();
        
        for($i = 0; $i<sizeof($ranking); $i++)
        {
            $return["Ranking"][$i]["Position"] = $ranking[$i]->Rank;

            $return["Ranking"][$i]["Character"] = getCharacterName($ranking[$i]->CharType);

            //Guild info
            $return["Ranking"][$i]["Guild"] = "No guild";
            $return["Ranking"][$i]["GuildMark"] = "--------";
            $guild = $userModel->getGuild($ranking[$i]->GuildID);
            if(!empty($guild))
            {
                $guildName = $userModel->getGuildSTR($ranking[$i]->GuildID)->Name;
                $return["Ranking"][$i]["Guild"] = $guildName;
                $guildMark = $guild->NID . "_" . $guild->MarkRevision . "." . $guild->MarkExtension;
                $guildMarkDir = __DIR__ . "/../../public/guildmark/" . $guildMark;
                $guildMarkURL = URL . "/guildmark/" . $guildMark;
                if(file_exists($guildMarkDir))
                {
                    $return["Ranking"][$i]["GuildMark"] = "<img src='".$guildMarkURL."' height='40' style='display: block;'>";
                }
		else
		{
		    $return["Ranking"][$i]["GuildMark"] = "<img src='" . URL . "/guildmark/defaultmark.png"."' height='40' style='display: block;'>";
		}

            }

            $return["Ranking"][$i]["Nickname"] = $ranking[$i]->Nick;

            $return["Ranking"][$i]["Level"] = $ranking[$i]->Level;

            $return["Ranking"][$i]["Exp"] = number_format($ranking[$i]->EXP);
        }
        
        $return["ThisRanking"] = "exp";
        $return["Character"] = strtolower(getCharacterName($this->tabType - 1));
        $return["Page"] = $this->page;
        $return["LastPage"] = $total_pages;
        $return["SearchFor"] = $this->searchFor;

        return $this->view("Rankings/exp", $return);
    }

    public function pvp($arguments, $category = "total")
    {
        $return = [];
        $return["Ranking"] = [];

        //Character page get
        if(isset($arguments[0]) && is_string($arguments[0]))
        {
            $this->tabType = getCharacterNumber(strtolower($arguments[0])) + 1;
        }

        //Page number get
        if(isset($arguments[1]) && is_numeric($arguments[1]))
        {
            $this->page = (int)$arguments[1];
            $this->page = ($this->page != 0) ? $this->page : 1;
        }

        //Gotocontainer
        if(isset($arguments[2]) && $arguments[2] == "container")
        {
            $return["gotoContainer"] = true;
        }

        $model = new \App\Models\Ranking();
        $total_results = $model->getRankingCount($category, $this->tabType);
        $total_pages = ceil($total_results / $this->resultPerPage);
        if($total_pages > $this->maxPages)
        {
            $total_pages = $this->maxPages;
        }

        //Get search for
        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST["search_nickname"]))
        {
            $return["gotoContainer"] = true;
            $form = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $this->searchFor = $form["search_nickname"];
            $search = $model->getRankingByUser($category, $this->tabType, $this->searchFor);
            if(!empty($search))
            {
                $searchUserRank = $search->Rank;
                $this->page = ceil($searchUserRank / $this->resultPerPage);
            }
            else
            {
                $this->resultPerPage = 0;
                $total_pages = 1;
            }
        }

        //Verifies if (&page=) exceeds the page limit (Except if searched for character)
        if ($this->page > $total_pages && !isset($_POST['search_nickname'])) {
            $this->page = 1;
        }

        $max_block = ceil($this->page / 10) * 10;
        $start = ceil($this->resultPerPage * ($this->page - 1) + 1);
        $end = ceil($start + $this->resultPerPage - 1);
        
        //Assemble the ranking array
        $ranking = $model->getRanking($category, $this->tabType, $start, $end);
        $userModel = new \App\Models\User();
        
        for($i = 0; $i<sizeof($ranking); $i++)
        {
            $return["Ranking"][$i]["Position"] = $ranking[$i]->Rank;

            $return["Ranking"][$i]["Character"] = getCharacterName($ranking[$i]->CharType);

            //Guild info
            $return["Ranking"][$i]["Guild"] = "No guild";
            $return["Ranking"][$i]["GuildMark"] = "--------";
            $guild = $userModel->getGuild($ranking[$i]->GuildID);
            if(!empty($guild))
            {
                $guildName = $userModel->getGuildSTR($ranking[$i]->GuildID)->Name;
                $return["Ranking"][$i]["Guild"] = $guildName;
                $guildMark = $guild->NID . "_" . $guild->MarkRevision . "." . $guild->MarkExtension;
                $guildMarkDir = __DIR__ . "/../../public/guildmark/" . $guildMark;
                $guildMarkURL = URL . "/guildmark/" . $guildMark;
                if(file_exists($guildMarkDir))
                {
                    $return["Ranking"][$i]["GuildMark"] = "<img src='".$guildMarkURL."' height='40' style='display: block;'>";
                }
            }

            $return["Ranking"][$i]["Nickname"] = $ranking[$i]->Nick;

            $return["Ranking"][$i]["Win"] = number_format($ranking[$i]->Win);

            $return["Ranking"][$i]["Lose"] = number_format($ranking[$i]->Lose);

            $return["Ranking"][$i]["WinRate"] = number_format($ranking[$i]->WinRate, 2);
        }
        
        $return["ThisRanking"] = $category;
        $return["Character"] = strtolower(getCharacterName($this->tabType - 1));
        $return["Page"] = $this->page;
        $return["LastPage"] = $total_pages;
        $return["SearchFor"] = $this->searchFor;

        return $this->view("Rankings/pvp", $return);
    }
}