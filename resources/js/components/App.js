import React from 'react';
import ReactDOM from 'react-dom';
import {BrowserRouter as Router, Route, Switch} from "react-router-dom";
import Feed from "./Feed";
import AddFeed from "./AddFeed"

function App() {
    return (
        <Router>
            <Switch>
                <Route exact path="/" component={Feed} />
                <Route path="/add-feeds" component={AddFeed}/>
            </Switch>
        </Router>
    );
}

export default App;

if (document.getElementById('app')) {
    ReactDOM.render(<App />, document.getElementById('app'));
}
