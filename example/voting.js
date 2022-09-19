escapeHtml = x =>
  x
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

getRandomId = () =>
  Math.random().toString(16).substr(2);

addIframe = () => {
  document.querySelector('#i').innerHTML = '<iframe name=x></iframe>';
  document.querySelector('iframe').onload = addCssLink;
};

addHiddenFormFields = () => {
  cssFile = (date + 2) % 9 + getRandomId();
  document.querySelector('#_').innerHTML =
    '<input name=f type=hidden value=' +
    pollFile +
    '><input name=c type=hidden value=' +
    cssFile +
    '>';
};

addCssLink = () =>
  document.querySelector('#i').innerHTML =
    "<link type=text/css rel='stylesheet noreferrer' referrerpolicy=no-referrer href=((form_action))" +
    document.querySelector('[name="s"]')
    .value
    .replace(/[+]/g, '-')
    .replace(/\//g, '_') +
    '/' +
    cssFile +
    '.css onload={{decodeCss}}()>';

decodeCss = () => {
  votes =
    decodeURIComponent(
      window.getComputedStyle(document.getElementById('_'), '::after').content
        .replace(/^[^'"]*['"]/, '')
        .replace(/['"][^'"]*$/, '')
        .replace(/[+]/g, ' ')
    );
  votes = votes && votes != 'none' ? JSON.parse(votes) : [];

  t = '<button onclick={{createNewPoll}}() type=button>New poll</button><table><tr><th>Edit';
  sum = [];
  for (i = 0; i < options.length; i++) {
    sum[i] = 0;
    t += '<th><input readonly value="' + escapeHtml(options[i]) + '">';
  }

  for (k = 0; k < votes.length; k++) {
    v = votes[k];
    t += '<tr><td><input type=radio name=i value=' + k + '><td>' + escapeHtml(v[0]);
    sum[0]++;
    j = 1;
    for (i = 1; i < options.length; i++) {
      c = 0;
      if (v[j] == i) {
        c = 'checked';
        sum[i]++;
        j++;
      }
      t += '<td><input disabled type=checkbox ' + c + '>';
    }
  }

  t += '<tr><td>';
  for (i = 0; i < options.length; i++)
    t += '<td>' + sum[i];

  t += '<tr><td><input type=radio name=i checked value=-1';
  for (i = 0; i < options.length; i++)
    t += '><td><input name=v[] ' +
    (i ? 'type=checkbox value=' + i : 'required');


  addHiddenFormFields();
  document.querySelector('#_').innerHTML +=
    t +
    '></table>' +
    (expiry - date + 8) % 9 +
    ' days left<input type=submit value=Vote><a target=_blank href="' +
    escapeHtml(
      viewerUrlPrefix ?
        viewerUrlPrefix + '#' + window_location.href.substr(0, window_location.href.lastIndexOf('#') - 1) :
        window_location.href
    ) +
    '">Share link</a>';
};

createNewPoll = () => {
  window_location.hash = viewerUrlPrefix ? '##' + viewerUrlPrefix : '';
  reloadOnAnchorChange();
};

updateAnchorOnCreatePoll = () => {
  l = '?h=' + date + getRandomId() + '&e=' + date;
  options = document.querySelectorAll('input');
  for (i = 2; i < options.length; i++)
    if (q = options[i].value)
      l += '&o=' + encodeURIComponent(q);
  window_location.hash = l + (viewerUrlPrefix ? '##' + viewerUrlPrefix : '');
  reloadOnAnchorChange();
};

reloadOnAnchorChange = () => {
  date = Math.trunc(new Date().getTime() / 1e8 % 9);

  viewerUrlPrefix = '';
  b = window_location.hash.substr(1);
  if (i = b.lastIndexOf('#') + 1) {
    viewerUrlPrefix = b.substr(i);
    b = b.substr(0, i - 2);
  }

  p = new URLSearchParams(b);
  options = p.getAll('o');
  pollFile = p.get('h');
  expiry = p.get('e');

  if (pollFile) {
    addHiddenFormFields();
    addIframe();
    document.forms[0].submit();
  } else
    document.querySelector('#_').innerHTML =
      '<button onclick={{updateAnchorOnCreatePoll}}() type=button>CreatePoll</button><label>Description<input></label><label>Choices</label>' +
      '<input>'.repeat(8);
};

window_location = window.location;
reloadOnAnchorChange();
