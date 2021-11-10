import React, {Component} from "react";
import {Link} from 'react-router-dom';

class Feed extends Component {

    render() {
        return(
            <div className="container">
                <div className="row">
                    <div className="col-md-12">
                        <div className="card">
                            <div className="card-header">
                                <h4>Feed
                                    <Link to={'add-feed'} className="btn btn-primary btn-sm float-right"> Add Feed</Link>
                                </h4>
                            </div>
                            <div className="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Feed;


