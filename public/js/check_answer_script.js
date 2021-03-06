function setSession(key,txt) {
  sessionStorage.setItem(key,txt);
}
function showtoastr(type,key) {
  if(sessionStorage.getItem(key)){
    if(type=='success'){
      toastr.success(sessionStorage.getItem(key));
      sessionStorage.removeItem(key);
    }
  }
}


var questionIDs = [];
var questionIdFormat = "q_";
var questionOptionsIds = [];

var retriveURL = $("input[name='information']").attr('retriveURL');
var subUrl = $("input[name='information']").attr('subUrl');
// var redirectUrl = $("input[name='learn_id']").attr('redirectUrl');
var data = ""
var total = 0;
var totalScore = 0;
var isAnswered = 1;

$( document ).ready(function() {
  function optionColor(el,questionId,optionId) {
    if(data[questionId]['answer'].includes(optionId)){
      if(data[questionId]['options_answer'].includes(optionId)){
        el.addClass("correct");
        data[questionId]['score']=data[questionId]['score']+(data[questionId]['mark']/Object.keys(data[questionId]['options_answer']).length );
      }else{
        el.addClass("wrong");
      }
    }

    return el;
  }
  function showScore(el,questionId) {
    el.text(data[questionId]['score']);
    return el;
  }

  function print(object) {
    console.log(object);
  }
  function removeId(el) {
    var temp = el.attr("id");
    el.attr("id","");
    el.attr("data_id",temp);
    return el;
  }
  function checkedOrNot(questionId,optionId) {
    if(data[questionId]['answer'].includes(optionId)) {
      return true;
    }
    return false;
  }
  function radioInputSet(el,name,id,checked=false) { // el = radio input element
    el.attr("name",name);
    el.attr("id",id);
    el.attr("checked",checked);
    el.attr("disabled",true);
    return el;
  }

  function checkboxInputSet(el,name,id,checked=false) { // el = check box input element
    el.attr("id",id);
    el.attr("name",name);
    el.attr("checked",checked);
    el.attr("disabled",true);
    return el;
  }

  function optionLabelSet(el,label,id) { // el = label element
    el.attr("for",id);
    el.html(label);
    return el;
  }

  function setUpQuestion(el,questionId) { // el = question element
    el.attr("id",questionId);
    el.removeClass("d-none");
    el.find(".question").html(data[questionId]['question']);
    return el;
  }
  function returnAnswerContainer(el) { //el = main question format
    return el.find('.answer form');
  }

  function textAnsFormat(questionId) {
    var answer = $("#text_answer_format").clone();
    answer = removeId(answer);
    answer.removeClass("d-none");
    answer.html(data[questionId]['answer']);
    return answer;
  }

  function multipleChoiceOptionAdd(el,questionId) { //el = option container
    var optionFormat = $("#multiple_choice_option_format");
    $.each(data[questionId]['options'],function(id,val){
      var optionFormatClone = optionFormat.clone();
      var radioInput = optionFormatClone.find("input:radio");
      var optionLabel = optionFormatClone.find('label');

      optionFormatClone = removeId(optionFormatClone);
      optionFormatClone.removeClass("d-none");

      radioInput = radioInputSet(radioInput,questionId,id,checked=checkedOrNot(questionId,id));
      optionLabel = optionLabelSet(optionLabel,val,id);
      optionFormatClone = optionColor(optionFormatClone,questionId,id);
      el.append(optionFormatClone);
    });
    return el;

  }

  function checkboxOptionAdd(el,questionId){ //el = optionContainer
    var optionFormat = $("#checkbox_option_format");
    $.each(data[questionId]['options'],function(id,val){
      var optionFormatClone = optionFormat.clone();
      var checkboxInput = optionFormatClone.find("input:checkbox");
      var optionLabel = optionFormatClone.find("label");

      optionFormatClone = removeId(optionFormatClone);
      optionFormatClone.removeClass("d-none");

      checkboxInput = checkboxInputSet(checkboxInput,id,id,checked=checkedOrNot(questionId,id));
      optionLabel = optionLabelSet(optionLabel,val,id);
      optionFormatClone = optionColor(optionFormatClone,questionId,id);
      el.append(optionFormatClone);
    });
    return el;
  }


  function addOption(el,questionId) { // el = answerFormat ex (#multiple_choice_format)

    if(data[questionId]['question_type'] == 'multiple_choice'){
      el = multipleChoiceOptionAdd(el,questionId);
    }else{
      el = checkboxOptionAdd(el, questionId);
    }
    return el;
  }

  function setUpForScoreInput(answerFormat,questionId) {
    var scoreInput = answerFormat.find("input");
    scoreInput.attr('max',data[questionId]['mark']);
    var maxMark = answerFormat.find(".max_mark");
    maxMark.text(data[questionId]['mark']);
    return answerFormat;
  }

  function makeAnswerFormat(el,questionId) {  //el = Main Question Format / questionFormatClone
    var answerContainer = returnAnswerContainer(el);  // return answer container (.answer form)
    var answerFormat = $("#"+data[questionId]['question_type']+"_format").clone();
    answerFormat = removeId(answerFormat);
    answerFormat.removeClass("d-none");
    if(data[questionId]['question_type'] == "short_answer"){
      answerFormat = setUpForScoreInput(answerFormat,questionId);
      answerFormat.append(textAnsFormat(questionId));
    }
    else if(data[questionId]['question_type'] == "paragraph"){
      answerFormat = setUpForScoreInput(answerFormat,questionId);
      answerFormat.append(textAnsFormat(questionId));
    }
    else{
      answerFormat = addOption(answerFormat,questionId);
    }
    answerContainer.append(answerFormat);
    return el;
  }

  function setUpAnswerFormat(el,questionId){ // el = questionFormatClone
    el = makeAnswerFormat(el,questionId)
    return el;
  }


  function loadHTMl() {
    var questionContainer = $('.questionContainer');
    var questionFormat = $('#questionFormat');
    $.each(data, function(key,val){

      questionIDs.push(key);
      questionOptionsIds.push(0);
      var questionFormatClone = questionFormat.clone();
      questionFormatClone = removeId(questionFormatClone);
      questionFormatClone.removeClass('d-none');
      data[key]['score'] = 0;
      questionFormatClone = setUpQuestion(questionFormatClone,key);
      questionFormatClone = setUpAnswerFormat(questionFormatClone,key);
      var scoreboard = questionFormatClone.find(".score");
      scoreboard = showScore(scoreboard,key);
      totalScore+=data[key]['score'];
      questionContainer.append(questionFormatClone);
      total+= parseInt(data[key]['mark']);
    });
    console.log(total);

  }

  $.ajax({
    url: retriveURL,
    type: 'get',
    dataType: 'json',
    contentType: 'application/json',
    success: function (responseData) {
      while(!responseData){

      }
      console.log(responseData);
      total = 0;
      // window.location.replace("/question/"+ learnId +"/edit");
      data = JSON.parse(responseData['answer']);
      loadHTMl();
      // console.log(data);
    },
    data: JSON.stringify(data)
  });
});


function scoreInput(el) {
  if(el.val()){
    var questionId = el.parents("li").attr("id");
    data[questionId]['score'] = parseFloat(el.val());
    totalScore+= data[questionId]['score'];
    console.log(totalScore);
    el.parents("li").find('.score').text(data[questionId]['score']);
  }


}

function submitAnswer() {
  // console.log(data);
  data['totalScore'] = totalScore;
  data['checked'] = 1;
  data['total'] = total;

  $.ajax({
    url: subUrl,
    type: 'post',
    dataType: 'json',
    contentType: 'application/json',
    success: function (responseData) {
      while(!responseData){

      }
      setSession('scriptChecked','Script Checked');
      console.log(responseData);

    },
    data: JSON.stringify(data)
  });

}
